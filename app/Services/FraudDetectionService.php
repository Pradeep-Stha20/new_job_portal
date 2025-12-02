<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class FraudDetectionService
{
    /**
     * Comprehensive fraud detection algorithm
     * Checks multiple risk factors to identify suspicious applications
     * 
     * Scoring system: 0-30 Safe, 31-60 Moderate, 61-100 High Risk
     * 
     * Factors: Duplicate apps (20pts), Spam (25pts), Profile (15pts), 
     * CV (20pts), Application rate (15pts), Account age (5pts)
     * 
     * @param JobApplication $application
     * @return float Fraud score (0-100)
     */
    public function calculateFraudScore(JobApplication $application): float
    {
        $fraudScore = 0;
        $fraudScore += $this->checkDuplicateApplications($application) * 20;
        $fraudScore += $this->analyzeCoverLetterSpam($application->cover_letter) * 25;
        $fraudScore += $this->checkProfileCompleteness($application->user) * 15;
        $fraudScore += $this->validateCVFile($application->cv) * 20;
        $fraudScore += $this->checkRapidApplicationPattern($application->user_id) * 15;
        $fraudScore += $this->checkAccountAge($application->user) * 5;
        return min(max(round($fraudScore, 2), 0), 100);
    }

    /**
     * Detect duplicate applications by same user
     * @param JobApplication $application
     * @return float Score 0-1
     */
    private function checkDuplicateApplications(JobApplication $application): float
    {
        $userId = $application->user_id;
        $jobId = $application->job_id;

        // Check: User already applied to this job
        $sameJobApplication = JobApplication::where('user_id', $userId)
            ->where('job_id', $jobId)
            ->where('id', '!=', $application->id)
            ->exists();

        if ($sameJobApplication) {
            return 1.0;
        }

        // Check: Many applications in last 24 hours
        $recentApplicationCount = JobApplication::where('user_id', $userId)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        if ($recentApplicationCount > 10) {
            return 0.8;
        }

        if ($recentApplicationCount > 5) {
            return 0.5;
        }

        return 0;
    }

    /**
     * Analyze cover letter for spam and suspicious keywords
     * @param string $coverLetter
     * @return float Score 0-1
     */
    private function analyzeCoverLetterSpam(string $coverLetter): float
    {
        $spamScore = 0;
        $coverLetterLower = strtolower($coverLetter);
        $wordCount = str_word_count($coverLetter);

        $spamKeywords = [
            'click here', 'buy now', 'limited time', 'free money',
            'guaranteed income', 'work from home earn', 'bitcoin',
            'cryptocurrency', 'investment opportunity', 'mlm',
            'http://', 'https://', 'www.', '.com',
            'telegram', 'whatsapp', 'contact me at'
        ];

        foreach ($spamKeywords as $keyword) {
            if (strpos($coverLetterLower, $keyword) !== false) {
                $spamScore += 0.15;
            }
        }

        if ($wordCount < 30) {
            $spamScore += 0.2;
        }

        if ($wordCount > 500) {
            $spamScore += 0.1;
        }

        $capsLetters = strlen($coverLetter) - strlen(preg_replace('/[A-Z]/', '', $coverLetter));
        $capsPercentage = $capsLetters / max($wordCount, 1);
        if ($capsPercentage > 0.5) {
            $spamScore += 0.15;
        }

        $punctuationCount = substr_count($coverLetter, '!') + substr_count($coverLetter, '?');
        if ($punctuationCount > $wordCount / 10) {
            $spamScore += 0.1;
        }

        if (preg_match('/(.)\1{5,}/', $coverLetter)) {
            $spamScore += 0.2;
        }

        return min($spamScore, 1.0);
    }

    /**
     * Check user profile completeness
     * @param User $user
     * @return float Score 0-1
     */
    private function checkProfileCompleteness(User $user): float
    {
        $completedFields = 0;
        $totalFields = 0;

        $fields = [
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'designation' => $user->designation,
            'skills' => $user->skills,
            'image' => $user->image,
        ];

        foreach ($fields as $field => $value) {
            $totalFields++;
            if (!empty($value)) {
                $completedFields++;
            }
        }

        $incompletenessRatio = (($totalFields - $completedFields) / $totalFields);
        return $incompletenessRatio;
    }

    /**
     * Validate CV file for suspicious characteristics
     * @param string|null $cvPath
     * @return float Score 0-1
     */
    private function validateCVFile(?string $cvPath): float
    {
        if (empty($cvPath)) {
            return 1.0;
        }

        if (!Storage::exists($cvPath)) {
            return 0.8;
        }

        try {
            $fileSize = Storage::size($cvPath);

            if ($fileSize < 5120) {
                return 0.7;
            }

            if ($fileSize > 10485760) {
                return 0.6;
            }

            $mimeType = Storage::mimeType($cvPath);
            $validMimes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];

            if (!in_array($mimeType, $validMimes)) {
                return 0.5;
            }

            return 0;
        } catch (\Exception $e) {
            return 0.5;
        }
    }

    /**
     * Check for rapid application pattern (bot/script activity)
     * @param int $userId
     * @return float Score 0-1
     */
    private function checkRapidApplicationPattern(int $userId): float
    {
        $lastHourCount = JobApplication::where('user_id', $userId)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($lastHourCount > 3) {
            return 1.0;
        }

        if ($lastHourCount > 1) {
            return 0.6;
        }

        $lastSixHoursCount = JobApplication::where('user_id', $userId)
            ->where('created_at', '>=', now()->subHours(6))
            ->count();

        if ($lastSixHoursCount > 15) {
            return 0.7;
        }

        if ($lastSixHoursCount > 8) {
            return 0.4;
        }

        return 0;
    }

    /**
     * Check account age - very new accounts are higher risk
     * @param User $user
     * @return float Score 0-1
     */
    private function checkAccountAge(User $user): float
    {
        $accountAge = now()->diffInHours($user->created_at);

        if ($accountAge < 1) {
            return 1.0;
        }

        if ($accountAge < 24) {
            return 0.5;
        }

        if ($accountAge < 168) {
            return 0.2;
        }

        return 0;
    }

    /**
     * Get detailed fraud analysis report
     * @param JobApplication $application
     * @return array Detailed fraud report
     */
    public function getFraudAnalysisReport(JobApplication $application): array
    {
        $fraudScore = $this->calculateFraudScore($application);
        
        return [
            'fraud_score' => $fraudScore,
            'risk_level' => $this->getRiskLevel($fraudScore),
            'duplicate_applications' => round($this->checkDuplicateApplications($application) * 100, 2),
            'cover_letter_spam' => round($this->analyzeCoverLetterSpam($application->cover_letter) * 100, 2),
            'profile_completeness' => round((1 - $this->checkProfileCompleteness($application->user)) * 100, 2),
            'cv_validation' => round((1 - $this->validateCVFile($application->cv)) * 100, 2),
            'application_pattern' => round($this->checkRapidApplicationPattern($application->user_id) * 100, 2),
            'account_age_risk' => round($this->checkAccountAge($application->user) * 100, 2),
            'recommendations' => $this->getRecommendations($application),
        ];
    }

    /**
     * Determine risk level based on fraud score
     * @param float $fraudScore
     * @return string Risk level
     */
    private function getRiskLevel(float $fraudScore): string
    {
        if ($fraudScore <= 30) {
            return 'safe';
        } elseif ($fraudScore <= 60) {
            return 'moderate';
        } else {
            return 'high';
        }
    }

    /**
     * Generate recommendations based on fraud analysis
     * @param JobApplication $application
     * @return array Recommended actions
     */
    private function getRecommendations(JobApplication $application): array
    {
        $fraudScore = $this->calculateFraudScore($application);
        $recommendations = [];

        if ($fraudScore >= 80) {
            $recommendations[] = 'Reject application - high fraud risk';
            $recommendations[] = 'Consider flagging user account';
        } elseif ($fraudScore >= 60) {
            $recommendations[] = 'Review carefully before approval';
            $recommendations[] = 'Verify user info independently';
        } elseif ($fraudScore >= 40) {
            $recommendations[] = 'Moderate risk - review if selecting';
        } else {
            $recommendations[] = 'Application appears legitimate';
        }

        if ($this->checkDuplicateApplications($application) > 0) {
            $recommendations[] = 'Duplicate applications detected';
        }

        if ($this->analyzeCoverLetterSpam($application->cover_letter) > 0.5) {
            $recommendations[] = 'Suspicious keywords in cover letter';
        }

        if ($this->validateCVFile($application->cv) > 0.5) {
            $recommendations[] = 'CV file has issues';
        }

        return $recommendations;
    }

    /**
     * Save fraud score to application record
     * @param JobApplication $application
     * @return void
     */
    public function saveFraudScore(JobApplication $application): void
    {
        $fraudScore = $this->calculateFraudScore($application);
        $status = $application->status;

        if ($fraudScore >= 85) {
            $status = 'flagged';
        }

        $application->update([
            'fraud_score' => $fraudScore,
            'status' => $status,
        ]);
    }

    /**
     * Batch process fraud detection for multiple applications
     * @param array $applicationIds
     * @return array Results
     */
    public function batchProcessFraudDetection(array $applicationIds): array
    {
        $results = [];

        foreach ($applicationIds as $appId) {
            $application = JobApplication::find($appId);
            if ($application) {
                $this->saveFraudScore($application);
                $results[$appId] = [
                    'fraud_score' => $application->fraud_score,
                    'status' => $application->status,
                ];
            }
        }

        return $results;
    }
}
