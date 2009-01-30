SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `remote_labs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `remote_labs` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `remote_labs` (
  `remote_lab_id` INT(8) NOT NULL AUTO_INCREMENT ,
  `gateway_url` VARCHAR(255) NULL ,
  `name` VARCHAR(255) NULL ,
  `time` TIMESTAMP NOT NULL ,
  `type` TINYINT(3) NULL ,
  `course_id` MEDIUMINT(8) NULL ,
  PRIMARY KEY (`remote_lab_id`) )
ENGINE = MyISAM;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `experiment_sets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `experiment_sets` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `experiment_sets` (
  `experiment_set_id` INT NOT NULL AUTO_INCREMENT ,
  `remote_lab_id` INT NOT NULL ,
  `set_code` VARCHAR(255) NULL ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`experiment_set_id`) ,
  CONSTRAINT `fk_experiment_sets_remote_labs`
    FOREIGN KEY (`remote_lab_id` )
    REFERENCES `mydb`.`remote_labs` (`remote_lab_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;

SHOW WARNINGS;
CREATE INDEX `fk_experiment_sets_remote_labs` ON `experiment_sets` (`remote_lab_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `experiments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `experiments` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `experiments` (
  `experiment_id` INT(8) NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NULL ,
  `package_id` MEDIUMINT(8) NULL ,
  `maxallowedtimes` INT(8) NULL ,
  `visible` BOOLEAN NULL ,
  `reservation_duration` MEDIUMINT(8) NULL ,
  `start` TIMESTAMP NULL ,
  `end` TIMESTAMP NULL ,
  `time` TIMESTAMP NULL ,
  PRIMARY KEY (`experiment_id`) )
ENGINE = MyISAM;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `experiments_es`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `experiments_es` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `experiments_es` (
  `experiments_es_id` INT(8) NOT NULL AUTO_INCREMENT ,
  `experiment_set_id` INT(8) NOT NULL ,
  `experiment_id` INT(8) NOT NULL ,
  PRIMARY KEY (`experiments_es_id`) ,
  CONSTRAINT `fk_experiments_es_experiment_sets`
    FOREIGN KEY (`experiment_set_id` )
    REFERENCES `mydb`.`experiment_sets` (`experiment_set_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_experiments_es_experiments`
    FOREIGN KEY (`experiment_id` )
    REFERENCES `mydb`.`experiments` (`experiment_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;

SHOW WARNINGS;
CREATE INDEX `fk_experiments_es_experiment_sets` ON `experiments_es` (`experiment_set_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_experiments_es_experiments` ON `experiments_es` (`experiment_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `reservations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `reservations` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `reservations` (
  `reservation_id` INT(8) NOT NULL AUTO_INCREMENT ,
  `experiments_es_id` INT(8) NOT NULL ,
  `member_id` INT(8) NULL ,
  `start_time` TIMESTAMP NULL ,
  PRIMARY KEY (`reservation_id`) ,
  CONSTRAINT `fk_reservations_experiments_es`
    FOREIGN KEY (`experiments_es_id` )
    REFERENCES `mydb`.`experiments_es` (`experiments_es_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;

SHOW WARNINGS;
CREATE INDEX `fk_reservations_experiments_es` ON `reservations` (`experiments_es_id` ASC) ;

SHOW WARNINGS;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

