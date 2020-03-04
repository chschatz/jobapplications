<?php

	namespace ITX\Jobapplications\Hooks;

	use ITX\Jobapplications\Domain\Model\Posting;
	use ITX\Jobapplications\Domain\Model\TtContent;
	use ITX\Jobapplications\Domain\Repository\PostingRepository;
	use ITX\Jobapplications\Domain\Repository\TtContentRepository;
	use ITX\Jobapplications\Utility\FrontendUriBuilder;
	use ITX\Jobapplications\Utility\GoogleIndexingApiConnector;
	use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
	use TYPO3\CMS\Core\DataHandling\DataHandler;
	use TYPO3\CMS\Core\Exception;
	use TYPO3\CMS\Core\Http\RequestFactory;
	use TYPO3\CMS\Core\Service\FlexFormService;
	use TYPO3\CMS\Core\Utility\DebugUtility;
	use TYPO3\CMS\Core\Utility\GeneralUtility;
	use TYPO3\CMS\Extbase\Domain\Model\Category;
	use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
	use TYPO3\CMS\Extbase\Object\ObjectManager;
	use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;

	/**
	 * Class TCEmainHook
	 *
	 * Catches Record manipulation events
	 *
	 * @package ITX\Jobapplications\Hooks
	 */
	class TCEmainHook
	{
		/**
		 * @param                                          $command
		 * @param                                          $table
		 * @param                                          $uid
		 * @param                                          $value
		 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
		 *
		 * @throws \Exception
		 */
		public function processDatamap_postProcessFieldArray($command, $table, $uid, $value, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj)
		{
			if ($table === "tx_jobapplications_domain_model_posting")
			{
				$enabled = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ExtensionConfiguration::class)
																 ->get('jobapplications', 'indexing_api');
				if ($enabled === "0")
				{
					return;
				}

				if ($command === "update" || $command === "new")
				{
					$connector = new GoogleIndexingApiConnector();
					if ($value['hidden'] === '1')
					{
						$connector->updateGoogleIndex($uid, true);
					}
					else
					{
						$connector->updateGoogleIndex($uid);
					}
				}
			}
			else
			{
				return;
			}
		}

		/**
		 * @param string      $command
		 * @param string      $table
		 * @param             $id
		 * @param             $value
		 * @param bool        $commandIsProcessed
		 * @param DataHandler $dataHandler
		 * @param             $pasteUpdate
		 *
		 * @throws \Exception
		 */
		public function processCmdmap_deleteAction($table, $uid, array $record, &$recordWasDeleted, DataHandler $dataHandler)
		{
			if ($table === "tx_jobapplications_domain_model_posting")
			{
				$connector = new GoogleIndexingApiConnector();
				$connector->updateGoogleIndex($uid, true);
			}
		}
	}