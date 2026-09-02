<?php

declare(strict_types=1);

/** @var float $value */
/** @var string $size */
/** @var int $maxStars */

?>

<div class="stars-rating <?= $size ?>">
    <?php for ($i = 1; $i <= $maxStars; $i++): ?>
        <span<?= $i <= floor($value) ? ' class="fill-star"' : '' ?>>&nbsp;</span>
    <?php endfor; ?>
</div>
