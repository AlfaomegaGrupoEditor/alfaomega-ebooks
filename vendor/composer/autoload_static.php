<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcd53d4cc421fe0b70226223f299ac0ad
{
    public static $files = array (
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        'a4a119a56e50fbb293281d9a48007e0e' => __DIR__ . '/..' . '/symfony/polyfill-php80/bootstrap.php',
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
        'a1105708a18b76903365ca1c4aa61b02' => __DIR__ . '/..' . '/symfony/translation/Resources/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Php80\\' => 23,
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Contracts\\Translation\\' => 30,
            'Symfony\\Component\\Translation\\' => 30,
        ),
        'P' => 
        array (
            'Psr\\Clock\\' => 10,
        ),
        'C' => 
        array (
            'Carbon\\Doctrine\\' => 16,
            'Carbon\\' => 7,
        ),
        'A' => 
        array (
            'Automattic\\WooCommerce\\' => 23,
            'AlfaomegaEbooks\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Php80\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php80',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Contracts\\Translation\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/translation-contracts',
        ),
        'Symfony\\Component\\Translation\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/translation',
        ),
        'Psr\\Clock\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/clock/src',
        ),
        'Carbon\\Doctrine\\' => 
        array (
            0 => __DIR__ . '/..' . '/carbonphp/carbon-doctrine-types/src/Carbon/Doctrine',
        ),
        'Carbon\\' => 
        array (
            0 => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon',
        ),
        'Automattic\\WooCommerce\\' => 
        array (
            0 => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce',
        ),
        'AlfaomegaEbooks\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'AlfaomegaEbooks\\Http\\Controllers\\EbooksController' => __DIR__ . '/../..' . '/src/Http/Controllers/EbooksController.php',
        'AlfaomegaEbooks\\Http\\Middleware' => __DIR__ . '/../..' . '/src/Http/Middleware.php',
        'AlfaomegaEbooks\\Http\\RouteManager' => __DIR__ . '/../..' . '/src/Http/RouteManager.php',
        'AlfaomegaEbooks\\Services\\Alfaomega\\Api' => __DIR__ . '/../..' . '/src/Services/Alfaomega/Api.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Entities\\AbstractEntity' => __DIR__ . '/../..' . '/src/Services/eBooks/Entities/AbstractEntity.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Entities\\Alfaomega\\EbookPost' => __DIR__ . '/../..' . '/src/Services/eBooks/Entities/Alfaomega/EbookPost.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Entities\\Alfaomega\\EbookPostEntity' => __DIR__ . '/../..' . '/src/Services/eBooks/Entities/Alfaomega/EbookPostEntity.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Entities\\EntityContract' => __DIR__ . '/../..' . '/src/Services/eBooks/Entities/EntityContract.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Entities\\WooCommerce\\Attribute' => __DIR__ . '/../..' . '/src/Services/eBooks/Entities/WooCommerce/Attribute.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Entities\\WooCommerce\\Product' => __DIR__ . '/../..' . '/src/Services/eBooks/Entities/WooCommerce/Product.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Entities\\WooCommerce\\ProductEntity' => __DIR__ . '/../..' . '/src/Services/eBooks/Entities/WooCommerce/ProductEntity.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Entities\\WooCommerce\\Tag' => __DIR__ . '/../..' . '/src/Services/eBooks/Entities/WooCommerce/Tag.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Entities\\WooCommerce\\Variant' => __DIR__ . '/../..' . '/src/Services/eBooks/Entities/WooCommerce/Variant.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Entities\\WooCommerce\\WooAbstractEntity' => __DIR__ . '/../..' . '/src/Services/eBooks/Entities/WooCommerce/WooAbstractEntity.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Managers\\AbstractManager' => __DIR__ . '/../..' . '/src/Services/eBooks/Managers/AbstractManager.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Managers\\EbookManager' => __DIR__ . '/../..' . '/src/Services/eBooks/Managers/EbookManager.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Managers\\QueueManager' => __DIR__ . '/../..' . '/src/Services/eBooks/Managers/QueueManager.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Managers\\SettingsManager' => __DIR__ . '/../..' . '/src/Services/eBooks/Managers/SettingsManager.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Managers\\WooCommerceManager' => __DIR__ . '/../..' . '/src/Services/eBooks/Managers/WooCommerceManager.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Process\\AbstractProcess' => __DIR__ . '/../..' . '/src/Services/eBooks/Process/AbstractProcess.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Process\\ImportEbook' => __DIR__ . '/../..' . '/src/Services/eBooks/Process/ImportEbook.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Process\\LinkProduct' => __DIR__ . '/../..' . '/src/Services/eBooks/Process/LinkProduct.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Process\\ProcessContract' => __DIR__ . '/../..' . '/src/Services/eBooks/Process/ProcessContract.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Process\\RefreshEbook' => __DIR__ . '/../..' . '/src/Services/eBooks/Process/RefreshEbook.php',
        'AlfaomegaEbooks\\Services\\eBooks\\Service' => __DIR__ . '/../..' . '/src/Services/eBooks/Service.php',
        'Attribute' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Attribute.php',
        'Automattic\\WooCommerce\\Client' => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce/Client.php',
        'Automattic\\WooCommerce\\HttpClient\\BasicAuth' => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce/HttpClient/BasicAuth.php',
        'Automattic\\WooCommerce\\HttpClient\\HttpClient' => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce/HttpClient/HttpClient.php',
        'Automattic\\WooCommerce\\HttpClient\\HttpClientException' => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce/HttpClient/HttpClientException.php',
        'Automattic\\WooCommerce\\HttpClient\\OAuth' => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce/HttpClient/OAuth.php',
        'Automattic\\WooCommerce\\HttpClient\\Options' => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce/HttpClient/Options.php',
        'Automattic\\WooCommerce\\HttpClient\\Request' => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce/HttpClient/Request.php',
        'Automattic\\WooCommerce\\HttpClient\\Response' => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce/HttpClient/Response.php',
        'Carbon\\AbstractTranslator' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/AbstractTranslator.php',
        'Carbon\\Carbon' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Carbon.php',
        'Carbon\\CarbonConverterInterface' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/CarbonConverterInterface.php',
        'Carbon\\CarbonImmutable' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/CarbonImmutable.php',
        'Carbon\\CarbonInterface' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/CarbonInterface.php',
        'Carbon\\CarbonInterval' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/CarbonInterval.php',
        'Carbon\\CarbonPeriod' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/CarbonPeriod.php',
        'Carbon\\CarbonPeriodImmutable' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/CarbonPeriodImmutable.php',
        'Carbon\\CarbonTimeZone' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/CarbonTimeZone.php',
        'Carbon\\Cli\\Invoker' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Cli/Invoker.php',
        'Carbon\\Doctrine\\CarbonDoctrineType' => __DIR__ . '/..' . '/carbonphp/carbon-doctrine-types/src/Carbon/Doctrine/CarbonDoctrineType.php',
        'Carbon\\Doctrine\\CarbonImmutableType' => __DIR__ . '/..' . '/carbonphp/carbon-doctrine-types/src/Carbon/Doctrine/CarbonImmutableType.php',
        'Carbon\\Doctrine\\CarbonType' => __DIR__ . '/..' . '/carbonphp/carbon-doctrine-types/src/Carbon/Doctrine/CarbonType.php',
        'Carbon\\Doctrine\\CarbonTypeConverter' => __DIR__ . '/..' . '/carbonphp/carbon-doctrine-types/src/Carbon/Doctrine/CarbonTypeConverter.php',
        'Carbon\\Doctrine\\DateTimeDefaultPrecision' => __DIR__ . '/..' . '/carbonphp/carbon-doctrine-types/src/Carbon/Doctrine/DateTimeDefaultPrecision.php',
        'Carbon\\Doctrine\\DateTimeImmutableType' => __DIR__ . '/..' . '/carbonphp/carbon-doctrine-types/src/Carbon/Doctrine/DateTimeImmutableType.php',
        'Carbon\\Doctrine\\DateTimeType' => __DIR__ . '/..' . '/carbonphp/carbon-doctrine-types/src/Carbon/Doctrine/DateTimeType.php',
        'Carbon\\Exceptions\\BadComparisonUnitException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/BadComparisonUnitException.php',
        'Carbon\\Exceptions\\BadFluentConstructorException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/BadFluentConstructorException.php',
        'Carbon\\Exceptions\\BadFluentSetterException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/BadFluentSetterException.php',
        'Carbon\\Exceptions\\BadMethodCallException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/BadMethodCallException.php',
        'Carbon\\Exceptions\\EndLessPeriodException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/EndLessPeriodException.php',
        'Carbon\\Exceptions\\Exception' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/Exception.php',
        'Carbon\\Exceptions\\ImmutableException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/ImmutableException.php',
        'Carbon\\Exceptions\\InvalidArgumentException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/InvalidArgumentException.php',
        'Carbon\\Exceptions\\InvalidCastException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/InvalidCastException.php',
        'Carbon\\Exceptions\\InvalidDateException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/InvalidDateException.php',
        'Carbon\\Exceptions\\InvalidFormatException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/InvalidFormatException.php',
        'Carbon\\Exceptions\\InvalidIntervalException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/InvalidIntervalException.php',
        'Carbon\\Exceptions\\InvalidPeriodDateException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/InvalidPeriodDateException.php',
        'Carbon\\Exceptions\\InvalidPeriodParameterException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/InvalidPeriodParameterException.php',
        'Carbon\\Exceptions\\InvalidTimeZoneException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/InvalidTimeZoneException.php',
        'Carbon\\Exceptions\\InvalidTypeException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/InvalidTypeException.php',
        'Carbon\\Exceptions\\NotACarbonClassException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/NotACarbonClassException.php',
        'Carbon\\Exceptions\\NotAPeriodException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/NotAPeriodException.php',
        'Carbon\\Exceptions\\NotLocaleAwareException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/NotLocaleAwareException.php',
        'Carbon\\Exceptions\\OutOfRangeException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/OutOfRangeException.php',
        'Carbon\\Exceptions\\ParseErrorException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/ParseErrorException.php',
        'Carbon\\Exceptions\\RuntimeException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/RuntimeException.php',
        'Carbon\\Exceptions\\UnitException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/UnitException.php',
        'Carbon\\Exceptions\\UnitNotConfiguredException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/UnitNotConfiguredException.php',
        'Carbon\\Exceptions\\UnknownGetterException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/UnknownGetterException.php',
        'Carbon\\Exceptions\\UnknownMethodException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/UnknownMethodException.php',
        'Carbon\\Exceptions\\UnknownSetterException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/UnknownSetterException.php',
        'Carbon\\Exceptions\\UnknownUnitException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/UnknownUnitException.php',
        'Carbon\\Exceptions\\UnreachableException' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Exceptions/UnreachableException.php',
        'Carbon\\Factory' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Factory.php',
        'Carbon\\FactoryImmutable' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/FactoryImmutable.php',
        'Carbon\\Language' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Language.php',
        'Carbon\\Laravel\\ServiceProvider' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Laravel/ServiceProvider.php',
        'Carbon\\MessageFormatter\\MessageFormatterMapper' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/MessageFormatter/MessageFormatterMapper.php',
        'Carbon\\PHPStan\\AbstractMacro' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/PHPStan/AbstractMacro.php',
        'Carbon\\PHPStan\\Macro' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/PHPStan/Macro.php',
        'Carbon\\PHPStan\\MacroExtension' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/PHPStan/MacroExtension.php',
        'Carbon\\PHPStan\\MacroScanner' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/PHPStan/MacroScanner.php',
        'Carbon\\Traits\\Boundaries' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Boundaries.php',
        'Carbon\\Traits\\Cast' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Cast.php',
        'Carbon\\Traits\\Comparison' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Comparison.php',
        'Carbon\\Traits\\Converter' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Converter.php',
        'Carbon\\Traits\\Creator' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Creator.php',
        'Carbon\\Traits\\Date' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Date.php',
        'Carbon\\Traits\\DeprecatedProperties' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/DeprecatedProperties.php',
        'Carbon\\Traits\\Difference' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Difference.php',
        'Carbon\\Traits\\IntervalRounding' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/IntervalRounding.php',
        'Carbon\\Traits\\IntervalStep' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/IntervalStep.php',
        'Carbon\\Traits\\Localization' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Localization.php',
        'Carbon\\Traits\\Macro' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Macro.php',
        'Carbon\\Traits\\MagicParameter' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/MagicParameter.php',
        'Carbon\\Traits\\Mixin' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Mixin.php',
        'Carbon\\Traits\\Modifiers' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Modifiers.php',
        'Carbon\\Traits\\Mutability' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Mutability.php',
        'Carbon\\Traits\\ObjectInitialisation' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/ObjectInitialisation.php',
        'Carbon\\Traits\\Options' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Options.php',
        'Carbon\\Traits\\Rounding' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Rounding.php',
        'Carbon\\Traits\\Serialization' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Serialization.php',
        'Carbon\\Traits\\Test' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Test.php',
        'Carbon\\Traits\\Timestamp' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Timestamp.php',
        'Carbon\\Traits\\ToStringFormat' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/ToStringFormat.php',
        'Carbon\\Traits\\Units' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Units.php',
        'Carbon\\Traits\\Week' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Traits/Week.php',
        'Carbon\\Translator' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/Translator.php',
        'Carbon\\TranslatorImmutable' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/TranslatorImmutable.php',
        'Carbon\\TranslatorStrongTypeInterface' => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon/TranslatorStrongTypeInterface.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'PhpToken' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/PhpToken.php',
        'Psr\\Clock\\ClockInterface' => __DIR__ . '/..' . '/psr/clock/src/ClockInterface.php',
        'Stringable' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Stringable.php',
        'Symfony\\Component\\Translation\\Catalogue\\AbstractOperation' => __DIR__ . '/..' . '/symfony/translation/Catalogue/AbstractOperation.php',
        'Symfony\\Component\\Translation\\Catalogue\\MergeOperation' => __DIR__ . '/..' . '/symfony/translation/Catalogue/MergeOperation.php',
        'Symfony\\Component\\Translation\\Catalogue\\OperationInterface' => __DIR__ . '/..' . '/symfony/translation/Catalogue/OperationInterface.php',
        'Symfony\\Component\\Translation\\Catalogue\\TargetOperation' => __DIR__ . '/..' . '/symfony/translation/Catalogue/TargetOperation.php',
        'Symfony\\Component\\Translation\\Command\\TranslationPullCommand' => __DIR__ . '/..' . '/symfony/translation/Command/TranslationPullCommand.php',
        'Symfony\\Component\\Translation\\Command\\TranslationPushCommand' => __DIR__ . '/..' . '/symfony/translation/Command/TranslationPushCommand.php',
        'Symfony\\Component\\Translation\\Command\\TranslationTrait' => __DIR__ . '/..' . '/symfony/translation/Command/TranslationTrait.php',
        'Symfony\\Component\\Translation\\Command\\XliffLintCommand' => __DIR__ . '/..' . '/symfony/translation/Command/XliffLintCommand.php',
        'Symfony\\Component\\Translation\\DataCollectorTranslator' => __DIR__ . '/..' . '/symfony/translation/DataCollectorTranslator.php',
        'Symfony\\Component\\Translation\\DataCollector\\TranslationDataCollector' => __DIR__ . '/..' . '/symfony/translation/DataCollector/TranslationDataCollector.php',
        'Symfony\\Component\\Translation\\DependencyInjection\\TranslationDumperPass' => __DIR__ . '/..' . '/symfony/translation/DependencyInjection/TranslationDumperPass.php',
        'Symfony\\Component\\Translation\\DependencyInjection\\TranslationExtractorPass' => __DIR__ . '/..' . '/symfony/translation/DependencyInjection/TranslationExtractorPass.php',
        'Symfony\\Component\\Translation\\DependencyInjection\\TranslatorPass' => __DIR__ . '/..' . '/symfony/translation/DependencyInjection/TranslatorPass.php',
        'Symfony\\Component\\Translation\\DependencyInjection\\TranslatorPathsPass' => __DIR__ . '/..' . '/symfony/translation/DependencyInjection/TranslatorPathsPass.php',
        'Symfony\\Component\\Translation\\Dumper\\CsvFileDumper' => __DIR__ . '/..' . '/symfony/translation/Dumper/CsvFileDumper.php',
        'Symfony\\Component\\Translation\\Dumper\\DumperInterface' => __DIR__ . '/..' . '/symfony/translation/Dumper/DumperInterface.php',
        'Symfony\\Component\\Translation\\Dumper\\FileDumper' => __DIR__ . '/..' . '/symfony/translation/Dumper/FileDumper.php',
        'Symfony\\Component\\Translation\\Dumper\\IcuResFileDumper' => __DIR__ . '/..' . '/symfony/translation/Dumper/IcuResFileDumper.php',
        'Symfony\\Component\\Translation\\Dumper\\IniFileDumper' => __DIR__ . '/..' . '/symfony/translation/Dumper/IniFileDumper.php',
        'Symfony\\Component\\Translation\\Dumper\\JsonFileDumper' => __DIR__ . '/..' . '/symfony/translation/Dumper/JsonFileDumper.php',
        'Symfony\\Component\\Translation\\Dumper\\MoFileDumper' => __DIR__ . '/..' . '/symfony/translation/Dumper/MoFileDumper.php',
        'Symfony\\Component\\Translation\\Dumper\\PhpFileDumper' => __DIR__ . '/..' . '/symfony/translation/Dumper/PhpFileDumper.php',
        'Symfony\\Component\\Translation\\Dumper\\PoFileDumper' => __DIR__ . '/..' . '/symfony/translation/Dumper/PoFileDumper.php',
        'Symfony\\Component\\Translation\\Dumper\\QtFileDumper' => __DIR__ . '/..' . '/symfony/translation/Dumper/QtFileDumper.php',
        'Symfony\\Component\\Translation\\Dumper\\XliffFileDumper' => __DIR__ . '/..' . '/symfony/translation/Dumper/XliffFileDumper.php',
        'Symfony\\Component\\Translation\\Dumper\\YamlFileDumper' => __DIR__ . '/..' . '/symfony/translation/Dumper/YamlFileDumper.php',
        'Symfony\\Component\\Translation\\Exception\\ExceptionInterface' => __DIR__ . '/..' . '/symfony/translation/Exception/ExceptionInterface.php',
        'Symfony\\Component\\Translation\\Exception\\IncompleteDsnException' => __DIR__ . '/..' . '/symfony/translation/Exception/IncompleteDsnException.php',
        'Symfony\\Component\\Translation\\Exception\\InvalidArgumentException' => __DIR__ . '/..' . '/symfony/translation/Exception/InvalidArgumentException.php',
        'Symfony\\Component\\Translation\\Exception\\InvalidResourceException' => __DIR__ . '/..' . '/symfony/translation/Exception/InvalidResourceException.php',
        'Symfony\\Component\\Translation\\Exception\\LogicException' => __DIR__ . '/..' . '/symfony/translation/Exception/LogicException.php',
        'Symfony\\Component\\Translation\\Exception\\MissingRequiredOptionException' => __DIR__ . '/..' . '/symfony/translation/Exception/MissingRequiredOptionException.php',
        'Symfony\\Component\\Translation\\Exception\\NotFoundResourceException' => __DIR__ . '/..' . '/symfony/translation/Exception/NotFoundResourceException.php',
        'Symfony\\Component\\Translation\\Exception\\ProviderException' => __DIR__ . '/..' . '/symfony/translation/Exception/ProviderException.php',
        'Symfony\\Component\\Translation\\Exception\\ProviderExceptionInterface' => __DIR__ . '/..' . '/symfony/translation/Exception/ProviderExceptionInterface.php',
        'Symfony\\Component\\Translation\\Exception\\RuntimeException' => __DIR__ . '/..' . '/symfony/translation/Exception/RuntimeException.php',
        'Symfony\\Component\\Translation\\Exception\\UnsupportedSchemeException' => __DIR__ . '/..' . '/symfony/translation/Exception/UnsupportedSchemeException.php',
        'Symfony\\Component\\Translation\\Extractor\\AbstractFileExtractor' => __DIR__ . '/..' . '/symfony/translation/Extractor/AbstractFileExtractor.php',
        'Symfony\\Component\\Translation\\Extractor\\ChainExtractor' => __DIR__ . '/..' . '/symfony/translation/Extractor/ChainExtractor.php',
        'Symfony\\Component\\Translation\\Extractor\\ExtractorInterface' => __DIR__ . '/..' . '/symfony/translation/Extractor/ExtractorInterface.php',
        'Symfony\\Component\\Translation\\Extractor\\PhpExtractor' => __DIR__ . '/..' . '/symfony/translation/Extractor/PhpExtractor.php',
        'Symfony\\Component\\Translation\\Extractor\\PhpStringTokenParser' => __DIR__ . '/..' . '/symfony/translation/Extractor/PhpStringTokenParser.php',
        'Symfony\\Component\\Translation\\Formatter\\IntlFormatter' => __DIR__ . '/..' . '/symfony/translation/Formatter/IntlFormatter.php',
        'Symfony\\Component\\Translation\\Formatter\\IntlFormatterInterface' => __DIR__ . '/..' . '/symfony/translation/Formatter/IntlFormatterInterface.php',
        'Symfony\\Component\\Translation\\Formatter\\MessageFormatter' => __DIR__ . '/..' . '/symfony/translation/Formatter/MessageFormatter.php',
        'Symfony\\Component\\Translation\\Formatter\\MessageFormatterInterface' => __DIR__ . '/..' . '/symfony/translation/Formatter/MessageFormatterInterface.php',
        'Symfony\\Component\\Translation\\IdentityTranslator' => __DIR__ . '/..' . '/symfony/translation/IdentityTranslator.php',
        'Symfony\\Component\\Translation\\Loader\\ArrayLoader' => __DIR__ . '/..' . '/symfony/translation/Loader/ArrayLoader.php',
        'Symfony\\Component\\Translation\\Loader\\CsvFileLoader' => __DIR__ . '/..' . '/symfony/translation/Loader/CsvFileLoader.php',
        'Symfony\\Component\\Translation\\Loader\\FileLoader' => __DIR__ . '/..' . '/symfony/translation/Loader/FileLoader.php',
        'Symfony\\Component\\Translation\\Loader\\IcuDatFileLoader' => __DIR__ . '/..' . '/symfony/translation/Loader/IcuDatFileLoader.php',
        'Symfony\\Component\\Translation\\Loader\\IcuResFileLoader' => __DIR__ . '/..' . '/symfony/translation/Loader/IcuResFileLoader.php',
        'Symfony\\Component\\Translation\\Loader\\IniFileLoader' => __DIR__ . '/..' . '/symfony/translation/Loader/IniFileLoader.php',
        'Symfony\\Component\\Translation\\Loader\\JsonFileLoader' => __DIR__ . '/..' . '/symfony/translation/Loader/JsonFileLoader.php',
        'Symfony\\Component\\Translation\\Loader\\LoaderInterface' => __DIR__ . '/..' . '/symfony/translation/Loader/LoaderInterface.php',
        'Symfony\\Component\\Translation\\Loader\\MoFileLoader' => __DIR__ . '/..' . '/symfony/translation/Loader/MoFileLoader.php',
        'Symfony\\Component\\Translation\\Loader\\PhpFileLoader' => __DIR__ . '/..' . '/symfony/translation/Loader/PhpFileLoader.php',
        'Symfony\\Component\\Translation\\Loader\\PoFileLoader' => __DIR__ . '/..' . '/symfony/translation/Loader/PoFileLoader.php',
        'Symfony\\Component\\Translation\\Loader\\QtFileLoader' => __DIR__ . '/..' . '/symfony/translation/Loader/QtFileLoader.php',
        'Symfony\\Component\\Translation\\Loader\\XliffFileLoader' => __DIR__ . '/..' . '/symfony/translation/Loader/XliffFileLoader.php',
        'Symfony\\Component\\Translation\\Loader\\YamlFileLoader' => __DIR__ . '/..' . '/symfony/translation/Loader/YamlFileLoader.php',
        'Symfony\\Component\\Translation\\LoggingTranslator' => __DIR__ . '/..' . '/symfony/translation/LoggingTranslator.php',
        'Symfony\\Component\\Translation\\MessageCatalogue' => __DIR__ . '/..' . '/symfony/translation/MessageCatalogue.php',
        'Symfony\\Component\\Translation\\MessageCatalogueInterface' => __DIR__ . '/..' . '/symfony/translation/MessageCatalogueInterface.php',
        'Symfony\\Component\\Translation\\MetadataAwareInterface' => __DIR__ . '/..' . '/symfony/translation/MetadataAwareInterface.php',
        'Symfony\\Component\\Translation\\Provider\\AbstractProviderFactory' => __DIR__ . '/..' . '/symfony/translation/Provider/AbstractProviderFactory.php',
        'Symfony\\Component\\Translation\\Provider\\Dsn' => __DIR__ . '/..' . '/symfony/translation/Provider/Dsn.php',
        'Symfony\\Component\\Translation\\Provider\\FilteringProvider' => __DIR__ . '/..' . '/symfony/translation/Provider/FilteringProvider.php',
        'Symfony\\Component\\Translation\\Provider\\NullProvider' => __DIR__ . '/..' . '/symfony/translation/Provider/NullProvider.php',
        'Symfony\\Component\\Translation\\Provider\\NullProviderFactory' => __DIR__ . '/..' . '/symfony/translation/Provider/NullProviderFactory.php',
        'Symfony\\Component\\Translation\\Provider\\ProviderFactoryInterface' => __DIR__ . '/..' . '/symfony/translation/Provider/ProviderFactoryInterface.php',
        'Symfony\\Component\\Translation\\Provider\\ProviderInterface' => __DIR__ . '/..' . '/symfony/translation/Provider/ProviderInterface.php',
        'Symfony\\Component\\Translation\\Provider\\TranslationProviderCollection' => __DIR__ . '/..' . '/symfony/translation/Provider/TranslationProviderCollection.php',
        'Symfony\\Component\\Translation\\Provider\\TranslationProviderCollectionFactory' => __DIR__ . '/..' . '/symfony/translation/Provider/TranslationProviderCollectionFactory.php',
        'Symfony\\Component\\Translation\\PseudoLocalizationTranslator' => __DIR__ . '/..' . '/symfony/translation/PseudoLocalizationTranslator.php',
        'Symfony\\Component\\Translation\\Reader\\TranslationReader' => __DIR__ . '/..' . '/symfony/translation/Reader/TranslationReader.php',
        'Symfony\\Component\\Translation\\Reader\\TranslationReaderInterface' => __DIR__ . '/..' . '/symfony/translation/Reader/TranslationReaderInterface.php',
        'Symfony\\Component\\Translation\\Test\\ProviderFactoryTestCase' => __DIR__ . '/..' . '/symfony/translation/Test/ProviderFactoryTestCase.php',
        'Symfony\\Component\\Translation\\Test\\ProviderTestCase' => __DIR__ . '/..' . '/symfony/translation/Test/ProviderTestCase.php',
        'Symfony\\Component\\Translation\\TranslatableMessage' => __DIR__ . '/..' . '/symfony/translation/TranslatableMessage.php',
        'Symfony\\Component\\Translation\\Translator' => __DIR__ . '/..' . '/symfony/translation/Translator.php',
        'Symfony\\Component\\Translation\\TranslatorBag' => __DIR__ . '/..' . '/symfony/translation/TranslatorBag.php',
        'Symfony\\Component\\Translation\\TranslatorBagInterface' => __DIR__ . '/..' . '/symfony/translation/TranslatorBagInterface.php',
        'Symfony\\Component\\Translation\\Util\\ArrayConverter' => __DIR__ . '/..' . '/symfony/translation/Util/ArrayConverter.php',
        'Symfony\\Component\\Translation\\Util\\XliffUtils' => __DIR__ . '/..' . '/symfony/translation/Util/XliffUtils.php',
        'Symfony\\Component\\Translation\\Writer\\TranslationWriter' => __DIR__ . '/..' . '/symfony/translation/Writer/TranslationWriter.php',
        'Symfony\\Component\\Translation\\Writer\\TranslationWriterInterface' => __DIR__ . '/..' . '/symfony/translation/Writer/TranslationWriterInterface.php',
        'Symfony\\Contracts\\Translation\\LocaleAwareInterface' => __DIR__ . '/..' . '/symfony/translation-contracts/LocaleAwareInterface.php',
        'Symfony\\Contracts\\Translation\\Test\\TranslatorTest' => __DIR__ . '/..' . '/symfony/translation-contracts/Test/TranslatorTest.php',
        'Symfony\\Contracts\\Translation\\TranslatableInterface' => __DIR__ . '/..' . '/symfony/translation-contracts/TranslatableInterface.php',
        'Symfony\\Contracts\\Translation\\TranslatorInterface' => __DIR__ . '/..' . '/symfony/translation-contracts/TranslatorInterface.php',
        'Symfony\\Contracts\\Translation\\TranslatorTrait' => __DIR__ . '/..' . '/symfony/translation-contracts/TranslatorTrait.php',
        'Symfony\\Polyfill\\Mbstring\\Mbstring' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/Mbstring.php',
        'Symfony\\Polyfill\\Php80\\Php80' => __DIR__ . '/..' . '/symfony/polyfill-php80/Php80.php',
        'Symfony\\Polyfill\\Php80\\PhpToken' => __DIR__ . '/..' . '/symfony/polyfill-php80/PhpToken.php',
        'UnhandledMatchError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/UnhandledMatchError.php',
        'ValueError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/ValueError.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcd53d4cc421fe0b70226223f299ac0ad::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcd53d4cc421fe0b70226223f299ac0ad::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitcd53d4cc421fe0b70226223f299ac0ad::$classMap;

        }, null, ClassLoader::class);
    }
}
