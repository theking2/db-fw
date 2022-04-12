CREATE 

VIEW `project`.`projectview` AS
    SELECT 
        `project`.`project`.`ID` AS `ProjectID`,
        `project`.`project`.`Number` AS `ProjectNr`,
        `project`.`project`.`Name` AS `ProjectName`,
        `project`.`projecttype`.`Name` AS `ProjectType`,
        `project`.`project`.`Status` AS `ProjectStatus`,
        `project`.`teacher`.`FullName` AS `Coach`
    FROM
        ((`project`.`project`
        JOIN `project`.`projecttype` ON (`project`.`project`.`TypeID` = `project`.`projecttype`.`ID`))
        JOIN `project`.`teacher` ON (`project`.`teacher`.`Abbr` = `project`.`project`.`Coach`))
    WHERE
        `project`.`project`.`Status` >= 0;


CREATE 

VIEW `project`.`studentprojectview` AS
    SELECT 
        `srp`.`ID` AS `ID`,
        `p`.`Name` AS `Name`,
        `p`.`Number` AS `ProjectNr`,
        `s`.`Fullname` AS `Fullname`,
        `r`.`Name` AS `Role`,
        `srp`.`Start` AS `Start`,
        `srp`.`End` AS `End`
    FROM
        (((`project`.`studentroleproject` `srp`
        JOIN `project`.`project` `p` ON (`p`.`ID` = `srp`.`ProjectID`))
        JOIN `project`.`projectrole` `r` ON (`r`.`ID` = `srp`.`ProjectRoleID`))
        JOIN `project`.`student` `s` ON (`s`.`ID` = `srp`.`StudentID`))