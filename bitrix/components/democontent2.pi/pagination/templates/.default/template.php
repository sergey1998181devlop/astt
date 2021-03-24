<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 01.10.2018
 * Time: 14:41
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}

$totalPages = ceil(($arParams['TOTAL'] / $arParams['LIMIT']));
if ($arParams['CURRENT_PAGE'] > $totalPages || $totalPages < 2) {
    return;
}
$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$iCurr = intval($arParams['CURRENT_PAGE']);
$iLastPage = $totalPages;
$iLeftLimit = 4;
$iRightLimit = 5;
$showLast = false;
$params = array();
if ($request->get('order')) {
    if ($request->get('order')) {
        switch ($request->get('order')) {
            case 'old':
            case 'expensive':
            case 'cheap':
            case 'popular':
                $params['order'] = $request->get('order');
                break;
        }
    }
}
if ($request->get('prop')) {
    $params['prop'] = $request->get('prop');
}
?>
    <div class="pagination text-center">
        <ul>
            <?php
            if ($iCurr > $iLeftLimit && $iCurr < ($iLastPage - $iRightLimit)) {
                for ($i = $iCurr - $iLeftLimit; $i <= $iCurr + $iRightLimit; $i++) {
                    if ($i == $iLastPage) {
                        $showLast = true;
                    }

                    if ($i == 1) {
                        if ($i > 0 && $i <= $iLastPage) {
                            if ($i == $iCurr) {
                                ?>
                                <li><span><?= $i ?></span></li>
                                <?php
                            } else {
                                ?>
                                <li>
                                    <a href="<?= $arParams['URL'] ?><?= ((count($params) > 0) ? '?' . http_build_query($params) : '') ?>"
                                       rel="nofollow">
                                        <?= $i ?>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                    } else {
                        if ($i > 0 && $i <= $iLastPage) {
                            if ($i == $iCurr) {
                                ?>
                                <li><span><?= $i ?></span></li>
                                <?php
                            } else {
                                ?>
                                <li>
                                    <a href="<?= $arParams['URL'] ?>?page=<?= $i ?><?= ((count($params) > 0) ? '&' . http_build_query($params) : '') ?>"
                                       rel="nofollow">
                                        <?= $i ?>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                    }
                }
            } elseif ($iCurr <= $iLeftLimit) {
                $iSlice = 1 + $iLeftLimit - $iCurr;
                for ($i = 1; $i <= $iCurr + ($iRightLimit + $iSlice); $i++) {
                    if ($i == $iLastPage) {
                        $showLast = true;
                    }

                    if ($i == 1) {
                        if ($i > 0 && $i <= $iLastPage) {
                            if ($i == $iCurr) {
                                ?>
                                <li><span><?= $i ?></span></li>
                                <?php
                            } else {
                                ?>
                                <li>
                                    <a href="<?= $arParams['URL'] ?><?= ((count($params) > 0) ? '?' . http_build_query($params) : '') ?>"
                                       rel="nofollow">
                                        <?= $i ?>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                    } else {
                        if ($i > 0 && $i <= $iLastPage) {
                            if ($i == $iCurr) {
                                ?>
                                <li><span><?= $i ?></span></li>
                                <?php
                            } else {
                                ?>
                                <li>
                                    <a href="<?= $arParams['URL'] ?>?page=<?= $i ?><?= ((count($params) > 0) ? '&' . http_build_query($params) : '') ?>"
                                       rel="nofollow">
                                        <?= $i ?>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                    }
                }
            } else {
                $iSlice = $iRightLimit - ($iLastPage - $iCurr);
                for ($i = $iCurr - ($iLeftLimit + $iSlice); $i <= $iLastPage; $i++) {
                    if ($i == $iLastPage) {
                        $showLast = true;
                    }

                    if ($i == 1) {
                        if ($i > 0 && $i <= $iLastPage) {
                            if ($i == $iCurr) {
                                ?>
                                <li><span><?= $i ?></span></li>
                                <?php
                            } else {
                                ?>
                                <li>
                                    <a href="<?= $arParams['URL'] ?><?= ((count($params) > 0) ? '?' . http_build_query($params) : '') ?>"
                                       rel="nofollow">
                                        <?= $i ?>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                    } else {
                        if ($i > 0 && $i <= $iLastPage) {
                            if ($i == $iCurr) {
                                ?>
                                <li><span><?= $i ?></span></li>
                                <?php
                            } else {
                                ?>
                                <li>
                                    <a href="<?= $arParams['URL'] ?>?page=<?= $i ?><?= ((count($params) > 0) ? '&' . http_build_query($params) : '') ?>"
                                       rel="nofollow">
                                        <?= $i ?>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                    }
                }
            }

            if (!$showLast) {
                ?>
                <li><span>...</span></li>
                <li>
                    <a href="<?= $arParams['URL'] ?>?page=<?= $iLastPage ?><?= ((count($params) > 0) ? '&' . http_build_query($params) : '') ?>"
                       rel="nofollow">
                        <?= $iLastPage ?>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}