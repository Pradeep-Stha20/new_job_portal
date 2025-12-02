<?php

namespace App\Services;

use App\Models\Job;
use App\Models\User;
use App\Models\JobApplication;

class JobMatchingService
{
    /**
     * Calculate match score between user and job using multiple factors
     * Uses cosine similarity for skills matching and experience matching
     * 
     * Score breakdown:
     * - Skills match: 0-50 points (cosine similarity of skills)
     * - Experience match: 0-30 points (years match job requirement)
     * - Profile completion: 0-20 points (user profile completeness)
     * 
     * @param User $user
     * @param Job $job
     * @return float Match score (0-100)
     */
    public function calculateJobMatchScore(User $user, Job $job): float
    {
        $totalScore = 0;

        // 1. Calculate skills match (0-50 points) using cosine similarity
        $skillsScore = $this->calculateSkillsMatchScore($user, $job);
        $totalScore += $skillsScore;

        // 2. Calculate experience match (0-30 points)
        $experienceScore = $this->calculateExperienceMatchScore($user, $job);
        $totalScore += $experienceScore;

        // 3. Calculate profile completion score (0-20 points)
        $profileScore = $this->calculateProfileCompletionScore($user);
        $totalScore += $profileScore;

        // Ensure score is within 0-100 range
        return min(max(round($totalScore, 2), 0), 100);
    }

    /**
     * Calculate skills match score using cosine similarity algorithm
     * Cosine Similarity = (A · B) / (||A|| × ||B||)
     * 
     * @param User $user
     * @param Job $job
     * @return float Skills match score (0-50)
     */
    private function calculateSkillsMatchScore(User $user, Job $job): float
    {
        // Extract and normalize skills from user profile
        $userSkills = $this->extractSkills($user->skills);
        
        // Extract and normalize job required skills from keywords and description
        $jobSkills = $this->extractSkills($job->keywords);

        // If either has no skills, return 0
        if (empty($userSkills) || empty($jobSkills)) {
            return 0;
        }

        // Calculate intersection (matching skills)
        $matchingSkills = array_intersect($userSkills, $jobSkills);
        $matchCount = count($matchingSkills);

        // Calculate magnitude (total unique skills on each side)
        $userSkillsCount = count($userSkills);
        $jobSkillsCount = count($jobSkills);

        // Cosine similarity formula: (matches) / sqrt(user_skills * job_skills)
        $denominator = sqrt($userSkillsCount * $jobSkillsCount);
        
        if ($denominator == 0) {
            return 0;
        }

        $cosineSimilarity = ($matchCount / $denominator);

        // Convert to percentage and scale to 0-50 points
        $skillsScore = ($cosineSimilarity * 100) * 0.5;

        return min($skillsScore, 50);
    }

    /**
     * Calculate experience match score
     * Perfect match: when user experience >= job requirement
     * Partial match: scales down if user has less experience
     * 
     * @param User $user
     * @param Job $job
     * @return float Experience match score (0-30)
     */
    private function calculateExperienceMatchScore(User $user, Job $job): float
    {
        $userExperience = $user->years_experience ?? 0;
        $jobExperience = (int)$job->experience;

        // Full points if user has equal or more experience
        if ($userExperience >= $jobExperience) {
            return 30;
        }

        // Partial points if user has some experience
        // Scale: (user_exp / job_exp) * 30
        if ($jobExperience > 0) {
            $experienceScore = ($userExperience / $jobExperience) * 30;
            return min($experienceScore, 30);
        }

        // No job experience requirement
        return 30;
    }

    /**
     * Calculate profile completion score
     * Checks if user has filled critical fields
     * 
     * @param User $user
     * @return float Profile completion score (0-20)
     */
    private function calculateProfileCompletionScore(User $user): float
    {
        $completionPercentage = 0;
        $totalFields = 0;

        // Check critical fields
        $criticalFields = ['name', 'email', 'mobile', 'designation', 'skills', 'years_experience'];

        foreach ($criticalFields as $field) {
            $totalFields++;
            if (!empty($user->$field)) {
                $completionPercentage++;
            }
        }

        // Convert to percentage and scale to 0-20 points
        $percentage = ($completionPercentage / $totalFields) * 100;
        $profileScore = ($percentage / 100) * 20;

        return min($profileScore, 20);
    }

    /**
     * Extract and normalize skills from text
     * Splits by comma, trims whitespace, converts to lowercase
     * 
     * @param string|null $skillsText
     * @return array Normalized skills array
     */
    private function extractSkills(?string $skillsText): array
    {
        if (empty($skillsText)) {
            return [];
        }

        // Split by comma and normalize
        $skills = array_map(function($skill) {
            return strtolower(trim($skill));
        }, explode(',', $skillsText));

        // Remove empty values
        $skills = array_filter($skills, function($skill) {
            return !empty($skill);
        });

        return array_values($skills); // Re-index array
    }

    /**
     * Get all matched jobs for a user with scores
     * Returns jobs sorted by match score (highest first)
     * 
     * @param User $user
     * @param int $limit
     * @return array Jobs with match scores
     */
    public function getMatchedJobs(User $user, int $limit = 10): array
    {
        $jobs = Job::where('status', 1)
            ->with('category', 'jobType')
            ->get();

        $matchedJobs = $jobs->map(function($job) use ($user) {
            $fitScore = $this->calculateJobMatchScore($user, $job);
            $job->fit_score = $fitScore;
            return $job;
        })->sortByDesc('fit_score')
            ->take($limit)
            ->values()
            ->toArray();

        return $matchedJobs;
    }

    /**
     * Save fit score to job application record
     * 
     * @param JobApplication $application
     * @return void
     */
    public function saveFitScore(JobApplication $application): void
    {
        $user = $application->user;
        $job = $application->job;

        $fitScore = $this->calculateJobMatchScore($user, $job);
        
        $application->update(['fit_score' => $fitScore]);
    }

    /**
     * Analyze applicants by fit score
     * Returns ranked applicants for a specific job
     * 
     * @param Job $job
     * @return array Ranked applicants
     */
    public function rankApplicantsByFitScore(Job $job): array
    {
        $applications = JobApplication::where('job_id', $job->id)
            ->with('user')
            ->get();

        return $applications->map(function($app) {
            if ($app->fit_score == 0) {
                // Calculate if not already calculated
                $this->saveFitScore($app);
            }
            return $app;
        })->sortByDesc('fit_score')
            ->values()
            ->toArray();
    }
}
