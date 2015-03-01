<?php


function build_user_tools($role)
{
	switch ($role) {

		// squad leader
		case 1:
		$tools = array(
			"Recruit" => array(
				'class' => 'addRct',
				'title' => 'Add new recruit',
				'descr' => 'Start the recruiting process with a division candidate',
				'icon' => 'plus-square text-success',
				'link' => '/recruiting',
				'disabled' => false
				),

			"Inactives" => array(
				'class' => 'revInactives',
				'title' => 'Review inactive members',
				'descr' => 'View inactive members and flag for removal',
				'icon' => 'flag',
				'link' => '/manage/inactive-members',
				'disabled' => false
				)
			);
		break;

		// platoon leader
		case 2:
		$tools = array(
			"Recruit" => array(
				'class' => 'addRct',
				'title' => 'Add new recruit',
				'descr' => 'Start the recruiting process with a division candidate',
				'icon' => 'plus-square text-success',
				'link' => '/recruiting',
				'disabled' => false
				),

			"DivisionStructureGenerator" => array(
				'class' => 'divGenerator',
				'title' => 'Generate division structure',
				'descr' => 'Generate a new division structure skeleton',
				'icon' => 'cog text-info',
				'link' => '#',
				'disabled' => false
				),

			"Inactives" => array(
				'class' => 'revInactives',
				'title' => 'Review inactive members',
				'descr' => 'View inactive members and flag for removal',
				'icon' => 'flag',
				'link' => '/manage/inactive-members',
				'disabled' => false
				)
			);
		break;

		// division leader
		case 3:
		$tools = array(
			"Recruit" => array(
				'class' => 'addRct',
				'title' => 'Add new recruit',
				'descr' => 'Start the recruiting process with a division candidate',
				'icon' => 'plus-square text-success',
				'link' => '/recruiting',
				'disabled' => false
				),

			"DivisionStructureGenerator" => array(
				'class' => 'divGenerator',
				'title' => 'Generate division structure',
				'descr' => 'Generate a new division structure skeleton',
				'icon' => 'cog text-info',
				'link' => '#',
				'disabled' => false
				),

			"Inactives" => array(
				'class' => 'revInactives',
				'title' => 'Review inactive reports',
				'descr' => 'View inactivity reports and prepare for removal',
				'icon' => 'flag',
				'link' => '/manage/inactive-members',
				'disabled' => false
				)
			);
		break;
	}
	return $tools;
}

?>