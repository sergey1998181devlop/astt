<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
if (!count($arResult)) {
    return;
}

if (method_exists($this, 'setFrameMode')) {
    $this->setFrameMode(true);
}
$i = 0;
foreach ($arResult as $item) {
    ?>
    <li>
        <a href="<?= $item['LINK'] ?>"><?= $item['TEXT'] ?></a>
    </li>
    <?php
}