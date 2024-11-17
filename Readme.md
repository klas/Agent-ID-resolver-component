# Agent ID resolver component


## Introduction
What started as a job test project has now expanded into an example Laravel Application: Agent Id resolver component - a lookup component that resolves an Agent from an Agent ID and the corresponding company name.

### The basic problem solved
AId-s come from various data sources in different formats, so we need to map them to the right agent. E.g. the following numbers are considered identical:
- 006674BA23
- 6674BA23
- 6674-BA23

### Features
* JSON API
* The following design patterns are in use beside standard Laravel patterns: 
  1. Builder Pattern to build the right filter chain for each company
  2. Chain of Responsibility for filter chain definitions
  3. Strategy to provide different resolving strategies: implemented are Filtering and Fuzzy Matching strategies. Active strategy is defined in the AppServiceProvider by binding to AidResolvingStrategyInterface.
* Tested using Http/Integration and Unit tests

## Installation
* Clone the Repo
* Install dependencies: `docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$(pwd):/var/www/html" \
  -w /var/www/html \
  laravelsail/php82-composer:latest \
  composer install --ignore-platform-reqs`
* If some Classes are missing: `docker run --rm --interactive --tty --volume $PWD:/app composer dump-autoload`
* copy .env-file: `cp .env.example .env`
* Start the Container: `vendor/bin/sail up -d`
* Run migrations und seeders: `vendor/bin/sail artisan migrate:fresh --seed`
* Publish the API: `vendor/bin/sail artisan install:api`

## Agent JSON API
To receive a correctly formatted JSON response including error messages send the request header `Accept: application/json`.

* Show: GET `/api/agent?aid={AID}&company={COMPANY NAME}` - requires AID und Company Name
* Example request: `http://localhost/api/agent?aid=00654564&company=Haftpflichtkasse Darmstadt`
* Example answer:
`  {
  "name": "Max Mustermann"
  }`

## Testing
* Run tests `vendor/bin/sail artisan test`

## Limitations
* Only AidStepFilteringResolvingStrategy and AidFuzzyResolvingStrategy are currently implemented
* There is no authorization check

## Examples of different formats
**Liability Insurance Magenstadt**
- 00654564
- 654564
- 654-564

**MMA**
- Q412548787
- 412548787

**Mama Insurance**
- 15154184714-000
- 15154184714
- 99/15154184714

**Bimbo Insurance:**
- 006674BA23
- 6674BA23
- 6674-BA23
  (Die Buchstaben sind Teil der Nummer)

**Die Hard**
- 54501R784
- 54501-R784
- 54501784
