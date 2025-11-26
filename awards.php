<?php
require_once __DIR__ . '/bootstrap.php';

session_start();

$db = Database::getInstance();
$seo = new SEO();
$seo->setTitle('Music Awards & Recognition')
    ->setDescription('Explore music industry awards including Grammy Awards, MTV Awards, Billboard Awards, and more. See winners, nominees, and award ceremonies.')
    ->setKeywords(['music awards', 'grammy', 'mtv awards', 'billboard awards', 'music industry awards'])
    ->setType('website');

// Get awards
$year = $_GET['year'] ?? date('Y');
$awardType = $_GET['award'] ?? '';

$sql = "SELECT * FROM awards WHERE year = :year";
$params = ['year' => $year];

if ($awardType) {
    $sql .= " AND award_name = :award";
    $params['award'] = $awardType;
}

$sql .= " ORDER BY award_name, category";

$awards = $db->fetchAll($sql, $params);

// Get available award types
$awardTypes = $db->fetchAll("SELECT DISTINCT award_name FROM awards ORDER BY award_name");
$availableYears = $db->fetchAll("SELECT DISTINCT year FROM awards ORDER BY year DESC");

include __DIR__ . '/includes/header.php';
?>

    <section class="chart-header">
        <div class="container">
            <h1 class="chart-title">MUSIC AWARDS</h1>
            <p style="text-align: center; color: #666; margin-top: 1rem;">Industry recognition and achievements</p>
            <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <select id="yearSelector" onchange="updateFilters()" style="padding: 0.5rem 1rem; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="">All Years</option>
                    <?php foreach ($availableYears as $y): ?>
                        <option value="<?php echo $y['year']; ?>" <?php echo $y['year'] == $year ? 'selected' : ''; ?>>
                            <?php echo $y['year']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select id="awardSelector" onchange="updateFilters()" style="padding: 0.5rem 1rem; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="">All Awards</option>
                    <?php foreach ($awardTypes as $type): ?>
                        <option value="<?php echo htmlspecialchars($type['award_name']); ?>" <?php echo $type['award_name'] === $awardType ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type['award_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </section>

    <section class="chart-content">
        <div class="container">
            <?php if (empty($awards)): ?>
                <div style="text-align: center; padding: 4rem;">
                    <h2>No Awards Data Available</h2>
                    <p>Awards data will be available once fetched from APIs.</p>
                </div>
            <?php else: ?>
                <div style="display: grid; gap: 2rem; margin-top: 2rem;">
                    <?php 
                    $currentAward = '';
                    foreach ($awards as $award): 
                        if ($currentAward !== $award['award_name']):
                            $currentAward = $award['award_name'];
                            if ($currentAward !== ''): ?>
                                </div>
                            <?php endif; ?>
                            <div class="info-card" style="padding: 2rem; background: rgba(255, 255, 255, 0.9); border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                <h2 style="color: #00d4aa; margin-bottom: 1.5rem; border-bottom: 2px solid #00d4aa; padding-bottom: 0.5rem;">
                                    <?php echo htmlspecialchars($award['award_name']); ?> - <?php echo $award['year']; ?>
                                </h2>
                        <?php endif; ?>
                        
                        <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(0, 212, 170, 0.05); border-radius: 8px; border-left: 4px solid #00d4aa;">
                            <h3 style="color: #333; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($award['category']); ?></h3>
                            <p style="color: #666; margin-bottom: 0.5rem;">
                                <strong>Winner:</strong> <span style="color: #00d4aa; font-weight: 600;"><?php echo htmlspecialchars($award['winner']); ?></span>
                            </p>
                            <?php if ($award['nominees']): 
                                $nominees = json_decode($award['nominees'], true);
                                if ($nominees && count($nominees) > 0): ?>
                                    <p style="color: #666; font-size: 0.9rem;">
                                        <strong>Nominees:</strong> <?php echo htmlspecialchars(implode(', ', $nominees)); ?>
                                    </p>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($award['description']): ?>
                                <p style="color: #666; font-size: 0.85rem; margin-top: 0.5rem;"><?php echo htmlspecialchars($award['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

<script>
function updateFilters() {
    const year = document.getElementById('yearSelector').value;
    const award = document.getElementById('awardSelector').value;
    let url = '?';
    if (year) url += 'year=' + year;
    if (award) url += (year ? '&' : '') + 'award=' + encodeURIComponent(award);
    window.location.href = url || '?';
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

