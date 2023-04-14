<?php
/**
* @version          SEBLOD 3.x Core ~ $Id: cck.php sebastienheraud $
* @package          SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url              https://www.seblod.com
* @editor           Octopoos - www.octopoos.com
* @copyright        Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license          GNU General Public License version 2 or later; see _LICENSE.php
**/

namespace Joomla\Plugin\Task\Cck\Extension;

use Exception;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status as TaskStatus;
use Joomla\Component\Scheduler\Administrator\Task\Task;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\SubscriberInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

// Cck
final class Cck extends CMSPlugin implements SubscriberInterface
{
	use TaskPluginTrait;

	protected const TASKS_MAP = [
		'plg_task_cck_processing' => [
			'langConstPrefix' => 'PLG_TASK_CCK_TASK_CCK_PROCESSING',
			'form'            => 'cck_processing',
			'method'          => 'callProcessing'
		]
	];

	// getSubscribedEvents
	public static function getSubscribedEvents(): array
	{
		return [
			'onTaskOptionsList'    => 'advertiseRoutines',
			'onExecuteTask'        => 'standardRoutineHandler',
			'onContentPrepareForm' => 'enhanceTaskItemForm',
		];
	}

	protected $autoloadLanguage = true;

	// __construct
	public function __construct(DispatcherInterface $dispatcher, array $config)
	{
		parent::__construct($dispatcher, $config);
	}

	// callProcessing
	protected function callProcessing(ExecuteTaskEvent $event): int
	{
		$params		=	$event->getArgument( 'params' );
		$processing	=	\JCckDatabase::loadObject( 'SELECT id, scriptfile, options FROM #__cck_more_processings WHERE published = 1 AND id ='.(int)$params->processing );

		if ( is_object( $processing ) ) {
			if ( $processing->scriptfile != '' &&  is_file( JPATH_SITE.'/'.$processing->scriptfile ) ) {
				$options	=	new \JRegistry( $processing->options );

				ob_start();
				include_once JPATH_SITE.'/'.$processing->scriptfile;
				ob_get_clean();
			}
		}

		return TaskStatus::OK;
	}
}
?>