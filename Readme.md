# Agent ID resolver component


## Introduction
What started as a job test project has now expanded into an more extensive example Laravel Application: an Agent Id resolver component that resolves an Agent from an Agent ID (that comes in various formats) and the corresponding company name.
### The basic problem solved
AId-s come from various data sources in different formats, so we need to map them to the right agent. E.g. the following numbers are considered identical:
- 006674BA23
- 6674BA23
- 6674-BA23

### Features
#### JSON API
* To receive a correctly formatted JSON response including error messages send the request header `Accept: application/json`.

* Show: GET `/api/agent?aid={AID}&company={COMPANY NAME}` - requires AID und Company Name
* Example request: `http://localhost/api/agent?aid=00654564&company=Liability Insurance Magenstadt`
* Example answer:
  `  {
  "name": "Max Mustermann"
  }`

#### Architecture - Design patterns
Beside standard Laravel structure, the code uses the following design patterns: 
* **Builder Pattern** to build the right filter chain for each company
* **Chain of Responsibility** for filter chain definitions
* **Strategy** to provide different resolving strategies. Active strategy is defined in the AppServiceProvider by binding to AidResolvingStrategyInterface. Implemented are: 
  * Filtering strategy the resolves name using different string filters and
  * Fuzzy Matching strategy that matches using fuzzy matching algorithms, mainly using the FuzzyWuzzy library. 

#### Various test examples
Tested using Laravel native API testing Http/Integration and Unit tests

## Limitations
* Only AidStepFilteringResolvingStrategy and AidFuzzyResolvingStrategy are currently implemented
* There is no authorization check

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

## Testing
* Run tests `vendor/bin/sail artisan test`

## Additional Info: Examples of different input formats
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
