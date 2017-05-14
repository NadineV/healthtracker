SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `tblPressure` (
`id` int(11) unsigned NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `pressure_high` int(11) NOT NULL,
  `pressure_low` int(11) NOT NULL,
  `puls` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `comment` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tblSession` (
`id` int(16) unsigned NOT NULL,
  `sessionCookie` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `userId` int(11) NOT NULL,
  `createTimestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tblUsers` (
`id` int(16) unsigned NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `height` float NOT NULL,
  `birthday` date NOT NULL,
  `sex` char(1) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `pass` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tblWeight` (
`id` mediumint(8) unsigned NOT NULL,
  `Date` date NOT NULL,
  `weight` float NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `userid` int(16) DEFAULT NULL,
  `comment` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `tblPressure`
 ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `tblSession`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `tblUsers`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `tblWeight`
 ADD PRIMARY KEY (`id`);


ALTER TABLE `tblPressure`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `tblSession`
MODIFY `id` int(16) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `tblUsers`
MODIFY `id` int(16) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `tblWeight`
MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
