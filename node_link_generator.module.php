<?php
/**
 * @file
 * Node Link Generator primary module file.
 */

function node_link_generator_node_operations() {

  // Define a new content action
  $operations['node_link_generator_generate'] = array(
    'label' => t('Generate menu links fot the selected content'), 
    'callback' => '_node_link_generator_redirect', 
    'callback arguments' => array('bulkupdate', array('message' => TRUE)),
  );
  
  return $operations;
}

function _node_link_generator_redirect (array $nids, $op, array $options = array()) {
  
  $_SESSION['node_link_generator_nids'] = $nids;
  drupal_goto('admin/node_link_generator/form');
 
}


function node_link_generator_menu() {

	$items['admin/node_link_generator/form'] = array(
		'title' => t('Node Link Generator > Select a menu'),
		'access arguments' => array('administer content'), 
		'page callback' => '_node_link_generator_parameters',
		'page arguments' => array(),
		'type' => MENU_CALLBACK
	);
			
	return $items;
}


function _node_link_generator_parameters() {

  return drupal_get_form('_node_link_generator_parameters_form');

}


function _node_link_generator_parameters_form() {


  $form['node_link_generator_menus'] = array(
    '#type' => 'checkboxes',
    '#options' => menu_get_menus($all = TRUE),
    '#required' => TRUE,
    '#multiple' => TRUE,
  );
  
   $form['submit_button'] = array(
      '#type' => 'submit',
      '#value' => t('Generate links'),
      '#weight' => 100,
    );
  
  $form['#validate'][] = '_node_link_generator_form_validate';
  $form['#submit'][] ='_node_link_generator_form_submit';

  return $form;
}

function _node_link_generator_form_validate($form, &$form_state) {

  if (!isset($form['node_link_generator_menus']['#value']) || count($form['node_link_generator_menus']['#value']) == 0) {
    form_set_error('title', t('Please select at least one menu to recieve the created node links.'));
  }

}

function _node_link_generator_form_submit($form, &$form_state) {


		echo "<pre>";
		print_r($_SESSION['node_link_generator_nids']);
		echo "</pre>";


		echo "<pre>";
		print_r($form['node_link_generator_menus']['#value']);
		echo "</pre>";


$menus = array(
  array(
    'menu_name' => 'menu_test_one',
    'title' => 'My Menu One',
    'description' => 'Lorem Ipsum',
  ),
  array(
    'menu_name' => 'menu_test_two',
    'title' => 'My Menu Two',
    'description' => 'Lorem Ipsum',
  ),
  array(
    'menu_name' => 'menu_test_three',
    'title' => 'My Menu Three',
    'description' => 'Lorem Ipsum',
  ),
);




$links = array(
  array(
    array(
      'link_title' => 'Link1',
      'link_path' => 'http://yourdomain.com/link1',
      'menu_name' => 'menu_test_one',
      'weight' => 0,
      'expanded' => 0,
    ),
    array(
      'link_title' => 'Link2',
      'link_path' => 'http://yourdomain.com/link2',
      'menu_name' => 'menu_test_one',
      'weight' => 1,
      'expanded' => 0,
    ),
  ),
  array(
    array(
      'link_title' => 'Link3',
      'link_path' => 'http://yourdomain.com/link3',
      'menu_name' => 'menu_test_two',
      'weight' => 0,
      'expanded' => 0,
    ),
    array(
      'link_title' => 'Link4',
      'link_path' => 'http://yourdomain.com/link4',
      'menu_name' => 'menu_test_two',
      'weight' => 1,
      'expanded' => 0,
    ),
  ),
  array(
    array(
      'link_title' => 'Link5',
      'link_path' => 'http://yourdomain.com/link5',
      'menu_name' => 'menu_test_three',
      'weight' => 0,
      'expanded' => 0,
    ),
    array(
      'link_title' => 'Link6',
      'link_path' => 'http://yourdomain.com/link6',
      'menu_name' => 'menu_test_three',
      'weight' => 1,
      'expanded' => 0,
    ),
  ),
);


// Save menu group into menu_custom table
foreach ($menus as $menu) {
  // Look the table first if the data does exist
  $exists = db_query("SELECT title FROM {menu_custom} WHERE menu_name=:menu_name", array(':menu_name' => $menu['menu_name']))->fetchField();
  // Save the record if the data does not exist
  if (!$exists) {
    menu_save($menu);
  }
}

//create menu links
$item = ''; 
foreach ($links as $layer1) {
  foreach ($layer1 as $link) {
    // Build an array of menu link 
    $item = array(
      'link_path' => $link['link_path'],
      'link_title' => $link['link_title'],
      'menu_name' => $link['menu_name'],
      'weight' => $link['weight'],
      'expanded' => $link['expanded'],
    );
    // Look the table first if the data does exist
    $exists = db_query("SELECT mlid from {menu_links} WHERE link_title=:link_title AND link_path=:link_path", array(':link_title' =>  $link['link_title'], ':link_path' => $link['link_path']))->fetchField();
    // Save the record if the data does not exist
    if (!$exists) {  
      menu_link_save($item);
    }
  }
}








 /*
    drupal_set_message( t('Generated links for @count nodes.', array('@count' => count($nids))));


  $nodes = node_load_multiple($nids);
  foreach ($nodes as $node) {
	var_dump( menu_get_menus($all = TRUE) );
	exit();

  }
 */ 

}

