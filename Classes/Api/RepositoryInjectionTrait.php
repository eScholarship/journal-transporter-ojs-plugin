<?php namespace JournalTransporterPlugin\Api;

/**
 * Very simple dependency injection based on attribute names.
 * Trait RepositoryInjection
 * @package JournalTransporterPlugin\Api
 */
trait RepositoryInjectionTrait
{
    /**
     *
     */
    protected function injectRepositories()
    {
        foreach(array_keys(get_class_vars(get_class($this))) as $attribute) {
            $matches = [];
            if(preg_match('/^(.+)Repository$/', $attribute, $matches)) {
                if($this->$attribute === null) {
                    $className = 'JournalTransporterPlugin\\Repository\\'.ucfirst($matches[1]);
                    $this->$attribute = new $className;
                }
            }
        }
    }
}