<?php
namespace NeueMedien;

class ProjectView extends \DB\DBRecord implements \DB\DBRecordInterface {
	static public function getPrimaryKeyName() { return "ProjectID"; }
	static public function getTableName() { return "ProjectView"; }
	static public function getFieldNames()
	{
		return 
		[
			'ProjectNr',
			'ProjectName',
			'ProjectDescription',
			'ProjectTypeName',
      'ProjectCoach',
      'ProjectStatus'
		];
	}
}