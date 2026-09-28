<?php

if (!function_exists('hipanao_badge_class')) {
	function hipanao_badge_class($value) {
		$v = strtolower(trim((string)$value));
		$map = array(
			'active'        => 'badge-status-active',
			'enroll'        => 'badge-status-enrolled',
			'enrolled'      => 'badge-status-enrolled',
			'register'      => 'badge-status-reserved',
			'assign'        => 'badge-status-assign',
			'inactive'      => 'badge-status-inactive',
			'in-active'     => 'badge-status-inactive',
			'graduated'     => 'badge-status-graduated',
			'pending'       => 'badge-status-pending',
			'reserved'      => 'badge-status-reserved',
			'suspended'     => 'badge-status-suspended',
			'dropped'       => 'badge-status-dropped',
			'completed'     => 'badge-status-completed',
			'for payment'   => 'badge-status-forpayment',
			'paid'          => 'badge-status-active',
			'unpaid'        => 'badge-status-forpayment',
			'major'         => 'badge-subject-major',
			'minor'         => 'badge-subject-minor',
			'administrator' => 'badge-role-administrator',
			'admin'         => 'badge-role-administrator',
			'editor'        => 'badge-role-editor',
			'author'        => 'badge-role-author',
			'subscriber'    => 'badge-role-subscriber',
			'male'          => 'badge-gender-male',
			'female'        => 'badge-gender-female',
		);
		return isset($map[$v]) ? $map[$v] : 'badge-secondary';
	}

	function hipanao_badge($value) {
		if ($value === null || $value === '') { return ''; }
		return '<span class="badge '.hipanao_badge_class($value).'">'.htmlspecialchars($value).'</span>';
	}

	function hipanao_action_btn($type, $attrName, $attrValue, $extraClass = '', $title = '') {
		$presets = array(
			'edit'    => array('btn-outline-warning', 'fa-pen',           $title ? $title : 'Edit'),
			'delete'  => array('btn-outline-danger',  'fa-trash',         $title ? $title : 'Delete'),
			'view'    => array('btn-outline-info',    'fa-eye',           $title ? $title : 'View'),
			'key'     => array('btn-outline-secondary','fa-key',          $title ? $title : 'Change Password'),
			'approve' => array('btn-outline-success', 'fa-clipboard-check',$title ? $title : 'Approve'),
			'account' => array('btn-outline-primary', 'fa-user-plus',      $title ? $title : 'Create Login Account'),
			'hasaccount' => array('btn-outline-secondary disabled', 'fa-user-check', $title ? $title : 'Already has a login account'),
		);
		$p = isset($presets[$type]) ? $presets[$type] : $presets['edit'];
		return '<button type="button" '.$attrName.'="'.$attrValue.'" class="btn '.$p[0].' btn-sm action-btn '.$extraClass.'" title="'.htmlspecialchars($p[2]).'"><i class="fa '.$p[1].'"></i></button>';
	}
}
?>
