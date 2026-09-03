<?php

declare(strict_types=1);

/** @var int $filledStars */
/** @var string $size */
/** @var int $maxStars */
?>

<div class="stars-rating <?= $size ?>">
    <?php foreach (range(1, $maxStars) as $i): ?>
        <span<?= $i <= $filledStars ? ' class="fill-star"' : '' ?>>&nbsp;</span>
    <?php endforeach; ?>
</div>
