CREATE VIEW `project`.`projectview` AS
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


CREATE VIEW `project`.`studentprojectview` AS
    SELECT 
        `srp`.`ID` AS `ID`,
        `p`.`ID` AS `ProjectID`,
        `p`.`Name` AS `Name`,
        `p`.`Number` AS `ProjectNr`,
        `s`.`ID` AS `StudentID`,
        `s`.`Fullname` AS `Fullname`,
        `r`.`Name` AS `Role`,
        `srp`.`Start` AS `Start`,
        `srp`.`End` AS `End`
    FROM
        (((`project`.`studentroleproject` `srp`
        JOIN `project`.`project` `p` ON (`p`.`ID` = `srp`.`ProjectID`))
        JOIN `project`.`projectrole` `r` ON (`r`.`ID` = `srp`.`ProjectRoleID`))
        JOIN `project`.`student` `s` ON (`s`.`ID` = `srp`.`StudentID`));

CREATE VIEW reservationview as
SELECT
    `r`.`ID` AS `ID`,
    `e`.`Name` AS `Name`,
    `e`.`Number` AS `Number`,
    `s`.`Fullname` AS `Fullname`,
    `r`.`Start` AS `Start`,
    `r`.`End` AS `End`
FROM
    (
        (
            `project`.`equipment_reservation` `r`
        JOIN `project`.`student` `s`
        ON
            (`s`.`ID` = `r`.`StudentID`)
        )
    JOIN `project`.`equipment` `e`
    ON
        (`e`.`ID` = `r`.`EquipmentID`)
    );

CREATE VIEW equipmentview as
SELECT
    `e`.`ID` AS `ID`,
    `e`.`Name` AS `Name`,
    `e`.`Number` AS `Number`,
    `e`.`Description` AS `Description`,
    `t`.`Name` AS `Type`
FROM
    (
        `project`.`equipment` `e`
    JOIN `project`.`equipmenttype` `t`
    ON
        (`e`.`EquipmentTypeID` = `t`.`ID`)
    )
ORDER BY
    `t`.`Name`,
    `e`.`Name`;

CREATE VIEW timesheetview AS
SELECT
    `t`.`ID` AS `ID`,
    `t`.`StudentID` AS `StudentID`,
    `s`.`Fullname` AS `Fullname`,
    `p`.`Name` AS `ProjectName`,
    `p`.`Number` AS `Number`,
    `t`.`Date` AS `Date`,
    `t`.`Minutes` AS `Minutes`
FROM
    (
        (
            `project`.`timesheet` `t`
        JOIN `project`.`student` `s`
        ON
            (`s`.`ID` = `t`.`StudentID`)
        )
    JOIN `project`.`project` `p`
    ON
        (`p`.`ID` = `t`.`ProjectID`)
    )