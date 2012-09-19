<?php
declare( encoding = "UTF8" ) ;
return array(
    'blogPage'        => array(
        'url'       => 'blog/page/[page:num?]',
        'controller'=> 'controllers\\blog',
        'function'    => 'index',
        'args'      => array('page'=> 1)
    ),
    'blog'            => array(
        'url'       => 'blog/[title:alphanum]',
        'controller'=> 'controllers\\blog',
        'function'    => 'blogPost'
    ),
    array(
        'url'      => 'blog',
        'redirect' => '/blog/page/1'
    ),
    array(
        'url' => 'blog/page/first',
        'alias' => array(
            'name'=>'blogPage',
            'args'=>array(1)
        )
    ),
    'blogPagination'  => array(
        'url'       => 'blog',
        'controller'=> 'controllers\\blog',
        'function'    => 'index',
        'args'      => array(1)
    ),
    'about'           => array(
        'url'        => 'about',
        'controller' => 'controllers\\about',
        'function'     => 'index'
    )
);
