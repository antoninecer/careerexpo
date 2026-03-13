<?php
namespace App;

class MatchingService {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function calculateMatch($candidateId, $jobId) {
        // Fetch candidate
        $stmt = $this->pdo->prepare("SELECT * FROM candidate_profiles WHERE id = ?");
        $stmt->execute([$candidateId]);
        $candidate = $stmt->fetch();

        // Fetch job
        $stmt = $this->pdo->prepare("SELECT * FROM jobs WHERE id = ?");
        $stmt->execute([$jobId]);
        $job = $stmt->fetch();

        if (!$candidate || !$job) return null;

        $score = 0;
        
        // 1. Seniority (40 points)
        if ($candidate['seniority'] === $job['seniority']) {
            $score += 40;
        } elseif ($this->isOverqualified($candidate['seniority'], $job['seniority'])) {
            $score += 25; // Overqualified is still good, but maybe not a perfect fit
        }

        // 2. Skills (40 points)
        $candidateSkills = array_map('trim', explode(',', strtolower((string)$candidate['skills'])));
        $jobSkills = array_map('trim', explode(',', strtolower((string)$job['skills'])));
        
        $candidateSkills = array_filter($candidateSkills);
        $jobSkills = array_filter($jobSkills);

        if (!empty($jobSkills)) {
            $matches = count(array_intersect($candidateSkills, $jobSkills));
            $skillScore = ($matches / count($jobSkills)) * 40;
            $score += round($skillScore);
        }

        // 3. Collaboration & Location (20 points)
        $locScore = 0;
        $jobLoc = strtolower((string)$job['location']);
        $candLoc = strtolower((string)$candidate['location']);
        
        if ($job['collaboration_type'] === 'remote') {
            $locScore = 20; // Job is remote, location doesn't matter
        } else {
            // Onsite or Hybrid
            if ($jobLoc === $candLoc) {
                $locScore = 20;
            } elseif ($candidate['preferred_collaboration'] === 'remote' && $job['collaboration_type'] !== 'remote') {
                $locScore = 0; // Candidate wants remote only, but job is onsite/hybrid
            } elseif ($candidate['preferred_collaboration'] === 'hybrid') {
                $locScore = 10; // Partial match for hybrid flexibility
            }
        }
        $score += $locScore;

        // Determine color
        $color = 'red';
        if ($score >= 75) {
            $color = 'green';
        } elseif ($score >= 45) {
            $color = 'orange';
        }

        return [
            'score' => (int)$score,
            'color' => $color,
            'comment' => "Shoda na základě seniority, dovedností a typu spolupráce."
        ];
    }

    private function isOverqualified($cand, $job) {
        $levels = ['junior' => 1, 'mid' => 2, 'senior' => 3, 'expert' => 4];
        return ($levels[$cand] ?? 0) > ($levels[$job] ?? 0);
    }

    public function updateAllMatchesForJob($jobId) {
        $stmt = $this->pdo->query("SELECT id FROM candidate_profiles");
        $candidates = $stmt->fetchAll();

        foreach ($candidates as $candidate) {
            $match = $this->calculateMatch($candidate['id'], $jobId);
            if (!$match) continue;

            $stmt = $this->pdo->prepare("INSERT INTO matches (candidate_id, job_id, score, color_code, comment) 
                VALUES (?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE score = VALUES(score), color_code = VALUES(color_code), comment = VALUES(comment)");
            
            $stmt->execute([
                $candidate['id'], $jobId, $match['score'], $match['color'], $match['comment']
            ]);
        }
    }
}
