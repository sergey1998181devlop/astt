<?php
$arUrlRewrite=array (
  0 => 
  array (
    'CONDITION' => '#^/api/v1/([a-z-]+)/($|\\?.+$)$#',
    'RULE' => 'namespace=democontent2.pi.api.v1&component=handler&params[method]=$1',
    'ID' => 'democontent2.pi:handler',
    'PATH' => '/local/democontent2_pi_prolog.php',
    'SOLUTION' => 'democontent2.pi',
    'SORT' => '1',
  ),
  1 => 
  array (
    'CONDITION' => '#^/payments/([\\da-zA-Z]{32}+)/($|\\?.+$)$#',
    'RULE' => 'component=payments&params[hash]=$1',
    'ID' => 'democontent2.pi:payments',
    'PATH' => '/local/democontent2_pi_prolog.php',
    'SOLUTION' => 'democontent2.pi',
    'SORT' => '2',
  ),
  2 => 
  array (
    'CONDITION' => '#^/task([\\d]+)-([\\d]+)/($|\\?.+$)$#',
    'RULE' => 'component=taskRedirect&params[iBlockId]=$2&params[itemId]=$1',
    'ID' => 'democontent2.pi:taskRedirect',
    'PATH' => '/local/democontent2_pi_prolog.php',
    'SOLUTION' => 'democontent2.pi',
    'SORT' => '3',
  ),
  3 => 
  array (
    'CONDITION' => '#^/users/([a-z-]+)/([\\da-z-]+)/($|\\?.+$)$#',
    'RULE' => 'component=users&params[iBlockType]=$1&params[iBlockCode]=$2',
    'ID' => 'democontent2.pi:users',
    'PATH' => '/local/democontent2_pi_template.php',
    'SOLUTION' => 'democontent2.pi',
    'SORT' => '4',
  ),
  4 => 
  array (
    'CONDITION' => '#^/users/([a-z-]+)/($|\\?.+$)$#',
    'RULE' => 'component=users&params[iBlockType]=$1',
    'ID' => 'democontent2.pi:users',
    'PATH' => '/local/democontent2_pi_template.php',
    'SOLUTION' => 'democontent2.pi',
    'SORT' => '5',
  ),
  5 => 
  array (
    'CONDITION' => '#^/users/($|\\?.+$)$#',
    'RULE' => 'component=users',
    'ID' => 'democontent2.pi:users',
    'PATH' => '/local/democontent2_pi_template.php',
    'SOLUTION' => 'democontent2.pi',
    'SORT' => '6',
  ),
  6 => 
  array (
    'CONDITION' => '#^/user/([\\d]+)/($|\\?.+$)$#',
    'RULE' => 'component=user.public.profile&params[id]=$1',
    'ID' => 'democontent2.pi:user.public.profile',
    'PATH' => '/local/democontent2_pi_template.php',
    'SOLUTION' => 'democontent2.pi',
    'SORT' => '7',
  ),
  7 => 
  array (
    'CONDITION' => '#^/user/items/([\\d]+)-([\\d]+)/($|\\?.+$)$#',
    'RULE' => 'component=user.items.detail&params[iBlockId]=$1&params[itemId]=$2',
    'ID' => 'democontent2.pi:user.items.detail',
    'PATH' => '/local/democontent2_pi_template.php',
    'SOLUTION' => 'democontent2.pi',
    'SORT' => '8',
  ),
  8 => 
  array (
    'CONDITION' => '#^/user/([a-z_-]+)/($|\\?.+$)$#',
    'RULE' => 'component=user.$1&params[componentName]=$1',
    'ID' => 'democontent2.pi:user.$1',
    'PATH' => '/local/democontent2_pi_template.php',
    'SOLUTION' => 'democontent2.pi',
    'SORT' => '9',
  ),
  9 => 
  array (
    'CONDITION' => '#^/city-([a-z-]+)/([a-z-]+)/([\\da-z-]+)/([\\da-z-]+)-([\\d]+)/($|\\?.+$)$#',
    'RULE' => 'component=detail&params[cityCode]=$1&params[iBlockType]=$2&params[iBlockCode]=$3&params[itemCode]=$4&params[itemId]=$5',
    'ID' => 'democontent2.pi:detail',
    'PATH' => '/local/democontent2_pi_template.php',
    'SOLUTION' => 'democontent2.pi',
    'SORT' => '10',
  ),
  10 => 
  array (
    'CONDITION' => '#^/([a-z-]+)/([\\da-z-]+)/([\\da-z-]+)-([\\d]+)/($|\\?.+$)$#',
    'RULE' => 'component=detail&params[iBlockType]=$1&params[iBlockCode]=$2&params[itemCode]=$3&params[itemId]=$4',
    'ID' => 'democontent2.pi:detail',
    'PATH' => '/local/democontent2_pi_template.php',
    'SOLUTION' => 'democontent2.pi',
    'SORT' => '11',
  ),
  11 => 
  array (
    'CONDITION' => '#^/city-([a-z-]+)/([a-z-]+)/([\\da-z-]+)/($|\\?.+$)$#',
    'RULE' => 'component=iblock.code&params[cityCode]=$1&params[iBlockType]=$2&params[iBlockCode]=$3',
    'ID' => 'democontent2.pi:iblock.code',
    'PATH' => '/local/democontent2_pi_template.php',
    'SOLUTION' => 'democontent2.pi',
    'SORT' => '12',
  ),
  12 => 
  array (
    'CONDITION' => '#^/city-([a-z-]+)/([a-z-]+)/($|\\?.+$)$#',
    'RULE' => 'component=iblock.type&params[cityCode]=$1&params[iBlockType]=$2',
    'ID' => 'democontent2.pi:iblock.type',
    'PATH' => '/local/democontent2_pi_template.php',
    'SOLUTION' => 'democontent2.pi',
    'SORT' => '13',
  ),
  13 => 
  array (
    'CONDITION' => '#^/([a-z-]+)/([\\da-z-]+)/($|\\?.+$)$#',
    'RULE' => 'component=iblock.code&params[iBlockType]=$1&params[iBlockCode]=$2',
    'ID' => 'democontent2.pi:iblock.code',
    'PATH' => '/local/democontent2_pi_template.php',
    'SOLUTION' => 'democontent2.pi',
    'SORT' => '14',
  ),
  14 => 
  array (
    'CONDITION' => '#^/([a-z-]+)/($|\\?.+$)$#',
    'RULE' => 'component=iblock.type&params[iBlockType]=$1',
    'ID' => 'democontent2.pi:iblock.type',
    'PATH' => '/local/democontent2_pi_template.php',
    'SOLUTION' => 'democontent2.pi',
    'SORT' => '15',
  ),
  15 => 
  array (
    'CONDITION' => '#^\\/?\\/mobileapp/jn\\/(.*)\\/.*#',
    'RULE' => 'componentName=$1',
    'ID' => '',
    'PATH' => '/bitrix/services/mobileapp/jn.php',
    'SORT' => '100',
  ),
  16 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => '100',
  ),
  17 =>
      array (
          'CONDITION' => '#^/user/employees/update/($|\\?.+$)#',
          'RULE' => 'component=user.employees.update',
          'ID' => 'democontent2.pi:user.$1',
          'PATH' => '/local/democontent2_pi_template.php',
          'SOLUTION' => 'democontent2.pi',
          'SORT' => '100',
      ),
    18 =>
        array (
            'CONDITION' => '#^/user/employees/taskEmpl/[0-9]+/($|\\?.+$)#',
            'RULE' => 'component=user.employees.taskEmpl',
            'ID' => 'democontent2.pi:user.$1',
            'PATH' => '/local/democontent2_pi_template.php',
            'SOLUTION' => 'democontent2.pi',
            'SORT' => '100',
        ),

   19 =>
        array (
            'CONDITION' => '#^/user/tasks/detail/[0-9]+/($|\\?.+$)#',
            'RULE' => 'component=user.employees.taskEmpl',
            'ID' => 'democontent2.pi:user.$1',
            'PATH' => '/local/democontent2_pi_template.php',
            'SOLUTION' => 'democontent2.pi',
            'SORT' => '100',
        ),
    20 =>
        array (
            'CONDITION' => '#^/user/moderation/detailModeration/[0-9]+/($|\\?.+$)#',
            'RULE' => 'component=user.moderation.detailTask',
            'ID' => 'democontent2.pi:user.$1',
            'PATH' => '/local/democontent2_pi_template.php',
            'SOLUTION' => 'democontent2.pi',
            'SORT' => '100',
        ),
    22 =>
        array (
            'CONDITION' => '#^/user/tasks/detail/[0-9]+/edit/#',
            'RULE' => 'component=user.edit.detailTaskUser',
            'ID' => 'democontent2.pi:user.$1',
            'PATH' => '/local/democontent2_pi_template.php',
            'SOLUTION' => 'democontent2.pi',
            'SORT' => '100',
        ),
    23 =>
        array (
            'CONDITION' => '#^/user/moderationCompany/#',
            'RULE' => 'component=user.moderationCompany',
            'ID' => 'democontent2.pi:user.$1',
            'PATH' => '/local/democontent2_pi_template.php',
            'SOLUTION' => 'democontent2.pi',
            'SORT' => '100',
        ),
    23 =>
        array (
            'CONDITION' => '#^/user/moderataion/company/[0-9]+/($|\\?.+$)#',
            'RULE' => 'component=user.moderationStatus.Company',
            'ID' => 'democontent2.pi:user.$1',
            'PATH' => '/local/democontent2_pi_template.php',
            'SOLUTION' => 'democontent2.pi',
            'SORT' => '100',
        ),

);
