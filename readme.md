# Flex Section System Command Line Interface

**Notice: This tool/system is experimental.**

A CLI tool for [Happy Medium](https://itsahappymedium.com)'s WordPress Flex Section System.


## Installation

### To a package (local)

```
composer require-dev itsahappymedium/flex-cli
./vendor/bin/flex --help
```

### To your system (global)

```
composer global require itsahappymedium/flix-cli
flex --help
```


## Setup

After you first install the tool, you will want to run `flex library:setup` which will pull down the flex sections library.


## Commands

```
library
  library:setup     Downloads the flex section module library
  library:update    Makes sure the flex section module library is up to date
module
  module:export     Exports a module from the current website project into the modules library repository
  module:import     Imports a flex section module into the current project
```


## License

MIT. See the [license.md file](license.md) for more info.