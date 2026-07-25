<?php
/**
 * matching_helper.php
 * دوال حساب نسبة التطابق (Match Score) بين الباحث عن عمل والوظيفة.
 *
 * التوزيع (مجموعه 100%):
 *  - Category (المجال)    : 25 نقطة
 *  - Skills (المهارات)    : 40 نقطة
 *  - Experience (الخبرة)  : 20 نقطة
 *  - Education (التعليم)  : 15 نقطة
 */

/**
 * @param array $seeker صف من جدول seekers (category_id, skills, years_of_experience, education)
 * @param array $job    صف من جدول jobs (category_id, skills, required_experience, education)
 * @return array ['total' => int, 'breakdown' => array, 'matched_skills_count' => int, 'required_skills_count' => int]
 */
function calculateMatchScore(array $seeker, array $job): array
{
    $breakdown = [
        'category'   => 0,
        'skills'     => 0,
        'experience' => 0,
        'education'  => 0,
    ];

    // 1) تطابق المجال - 25 نقطة
    if (!empty($seeker['category_id']) && !empty($job['category_id'])
        && (int)$seeker['category_id'] === (int)$job['category_id']) {
        $breakdown['category'] = 25;
    }

    // 2) تطابق المهارات - 40 نقطة (نسبة التقاطع بين مهارات الباحث والمطلوبة)
    $seekerSkills = array_filter(array_map('intval', explode(',', (string)($seeker['skills'] ?? ''))));
    $jobSkills    = array_filter(array_map('intval', explode(',', (string)($job['skills'] ?? ''))));

    $matchedSkills = array_intersect($seekerSkills, $jobSkills);
    $requiredCount = count($jobSkills);

    if ($requiredCount > 0) {
        $breakdown['skills'] = (int)round((count($matchedSkills) / $requiredCount) * 40);
    }

    // 3) تطابق الخبرة - 20 نقطة
    $requiredExp = (int)($job['required_experience'] ?? 0);
    $seekerExp   = (int)($seeker['years_of_experience'] ?? 0);

    if ($requiredExp <= 0) {
        $breakdown['experience'] = 20; // الشركة لم تحدد حد أدنى
    } elseif ($seekerExp >= $requiredExp) {
        $breakdown['experience'] = 20;
    } else {
        $breakdown['experience'] = (int)round(($seekerExp / $requiredExp) * 20);
    }

    // 4) تطابق التعليم - 15 نقطة
    $levels = [
        "Bachelor's Degree" => 1,
        "Master's Degree"   => 2,
        "Doctorate"         => 3,
    ];
    $seekerLevel = $levels[$seeker['education'] ?? ''] ?? 0;
    $jobLevel    = $levels[$job['education'] ?? ''] ?? 0;

    if ($jobLevel === 0) {
        $breakdown['education'] = 15; // لم يتم تحديد مستوى تعليمي مطلوب
    } elseif ($seekerLevel >= $jobLevel) {
        $breakdown['education'] = 15;
    } elseif ($seekerLevel === $jobLevel - 1) {
        $breakdown['education'] = 7; // أقل بدرجة واحدة فقط عن المطلوب
    }

    $total = array_sum($breakdown);

    return [
        'total'                 => min(100, $total),
        'breakdown'             => $breakdown,
        'matched_skills_count'  => count($matchedSkills),
        'required_skills_count' => $requiredCount,
    ];
}

/**
 * يرجع كلاس Bootstrap مناسب للـ badge حسب قيمة النسبة
 */
function matchScoreBadgeClass(int $score): string
{
    if ($score >= 75) return 'bg-success';
    if ($score >= 50) return 'bg-warning text-dark';
    return 'bg-danger';
}
