# Good COVID-19 New
Open Source COVID-19 statistic project – https://good-covid-19-news.com/
 
## About the project
Hello, my name is Artem and I'm a web developer.

I started this project to fight the [Negativity bias](https://en.wikipedia.org/wiki/Negativity_bias) of the pandemic statistic.
We are all affected even if not infected and virus' impact on global society are very strong and dramatic.

But we often neglect the positive dynamic and tent to focus on the bad news only.

### Technical details
Project build on Symfony web framework version 5.1 with PHP 7.2.

Multi-language support implemented with `.xlf` translation. 

## Participate
This project is intentionally Open Source.

You can help it by contributing: translation, code, fixes, new features, etc.

### Local deploy
After project cloned – perform typical deployment steps for Symfony project.
You can check `deploy/deploy.php` tasks to check what actions are performed durin deploy.

**General deployment steps:**
* clone
* create DB
* Obtain RapidAPI key for [Coronavirus Monitor API](https://rapidapi.com/astsiatsko/api/coronavirus-monitor)
* create `.evn.local` file
* setup database connection parameters and API key in `.env.local`
* install resources: `yarn install`
* compile resources: `yarn encore dev`  
* install vendors: `composer install`
* run migrations: `bin/console doctrine:migrations:migrate`

**Data manipulation steps:**
* dump countries data `bin/console app:collect:countries`
* check statistic steps `bin/console app:stats:calculate --help`

### "Help wanted" issues
Please check the issue page for issues with "help wanted" label. This is the most important issues for project.

### New translation
If you can translate project to your language - feel free to contribute!

* First of all – check [Symfony Translations documentation](https://symfony.com/doc/5.1/translation.html)
* Dump language translation for your locale. Use [language ISO 639-1 code](https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes):
`bin/console translation:update --output-format=xlf --xliff-version=1.2 --domain=messages --force es`
* translate dumped messages
* add new locale to `config/services.yaml` into `parameters.supported_locales`
* add language to route requirements in file `config/routes/annotations.yaml` for `controllers.requirements._locale`
* add language name in `translations/languages+intl-icu.en.xlf`

All language names are stored in one file only, no need to create translation for `languages` domain.

To add new language/region just add a new unit:
```xml
<trans-unit id="es" resname="es">
  <source>es</source>
  <target>Español</target>
</trans-unit>
``` 

### Discuss
Project Slack https://good-covid-19-news.slack.com/ 