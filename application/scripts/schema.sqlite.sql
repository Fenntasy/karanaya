
--
-- Structure de la table anime
--

CREATE TABLE anime (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(255) NOT NULL,
  parent INTEGER NOT NULL,
  FOREIGN KEY (parent) REFERENCES anime(parent)
) ;

-- --------------------------------------------------------

--
-- Structure de la table author
--

CREATE TABLE author (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(255) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Structure de la table language
--

CREATE TABLE language (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(3) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Structure de la table song
--

CREATE TABLE song (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(255) NOT NULL,
  author INTEGER NOT NULL,
  FOREIGN key (author) REFERENCES author(id)
) ;

-- --------------------------------------------------------

--
-- Structure de la table subType
--

CREATE TABLE subType (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(255) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Structure de la table type
--

CREATE TABLE type (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(255) NOT NULL,
  identifier VARCHAR(255) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Structure de la table video
--

CREATE TABLE video (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
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
  FOREIGN KEY (type) REFERENCES type(id),
  FOREIGN KEY (anime) REFERENCES anime(id),
  FOREIGN KEY (subType) REFERENCES subType(id),
  FOREIGN KEY (madeBy) REFERENCES users(id),
  FOREIGN KEY (song) REFERENCES song(id),
  FOREIGN KEY (language) REFERENCES language(id)
) ;


-- --------------------------------------------------------

-- 
-- Structure de la table users
--

CREATE TABLE users (
    id INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(32) NULL,
    password_salt VARCHAR(32) NULL,
    display_name VARCHAR(150) NULL
)
