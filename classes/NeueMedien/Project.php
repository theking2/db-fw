<?php
namespace NeueMedien;

class Project extends \DB\DBRecord implements \DB\DBRecordInterface {
	static public function getPrimaryKeyName() { return "ProjectID"; }
	static public function getTableName() { return "Project"; }
	static public function getFieldNames()
	{
		return 
		[
			'ProjectParentID',
			'ProjectNr',
			'ProjectName',
			'ProjectDescription',
			'ProjectTypeID',
      'CustomerID',
      'ProjectCoach',
      'ProjectStatus'
		];
	}
}