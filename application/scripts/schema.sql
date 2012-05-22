
--
-- Structure de la table anime
--

CREATE TABLE IF NOT EXISTS anime (
  id INTEGER NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  parent INTEGER NOT NULL,
  PRIMARY KEY (id),
  KEY parent (parent)
) ;

-- --------------------------------------------------------

--
-- Structure de la table author
--

CREATE TABLE IF NOT EXISTS author (
  id INTEGER NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
) ;

-- --------------------------------------------------------

--
-- Structure de la table language
--

CREATE TABLE IF NOT EXISTS language (
  id INTEGER unsigned NOT NULL AUTO_INCREMENT,
  name VARCHAR(3) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id)
) ;

-- --------------------------------------------------------

--
-- Structure de la table song
--

CREATE TABLE IF NOT EXISTS song (
  id INTEGER NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  author INTEGER NOT NULL,
  PRIMARY KEY (id),
  KEY author (author)
) ;

-- --------------------------------------------------------

--
-- Structure de la table subType
--

CREATE TABLE IF NOT EXISTS subType (
  id INTEGER NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
) ;

-- --------------------------------------------------------

--
-- Structure de la table type
--

CREATE TABLE IF NOT EXISTS type (
  id INTEGER NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  identifier VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
) ;

-- --------------------------------------------------------

--
-- Structure de la table video
--

CREATE TABLE IF NOT EXISTS video (
  id INTEGER NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  type INTEGER NOT NULL,
  typeNumber VARCHAR(2) NOT NULL,
  subType INTEGER NOT NULL,
  madeBy INTEGER NOT NULL,
  song INTEGER NOT NULL,
  anime INTEGER NOT NULL,
  language INTEGER NOT NULL,
  duration INTEGER NOT NULL,
  extension VARCHAR(4) NOT NULL,
  comment VARCHAR(255) NOT NULL,
  PRIMARY KEY (id),
  KEY type (type,subType,madeBy,song),
  KEY anime (anime),
  KEY type_2 (type),
  KEY subType (subType),
  KEY madeBy (madeBy),
  KEY song (song),
  KEY language (language)
) ;


-- --------------------------------------------------------

-- 
-- Structure de la table users
--

CREATE TABLE users (
    id INTEGER  NOT NULL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(32) NULL,
    password_salt VARCHAR(32) NULL,
    display_name VARCHAR(150) NULL
)
