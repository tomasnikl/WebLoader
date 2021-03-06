<?php

/**
 *
 * Copyright (c) Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md
 * that was distributed with this source code.
 *
 */

declare(strict_types = 1);

namespace WebLoader\Tests;

require_once 'bootstrap.php';

use Tester\Assert;
use WebLoader\Exception;


/**
 * @testCase
 */
final class ExceptionsTestCase extends AbstractTestCase
{

	public function testDuplicatedCssFilesCollectionException(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()->createCssFilesCollection('test');
			$this->getWebLoader()->createCssFilesCollection('test');
		}, Exception::class, 'CSS files collection "test" already exists.');
	}


	public function testDuplicatedJsFilesCollectionException(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()->createJsFilesCollection('test');
			$this->getWebLoader()->createJsFilesCollection('test');
		}, Exception::class, 'JS files collection "test" already exists.');
	}


	public function testDuplicatedFilesCollectionsContainerException(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()->createFilesCollectionsContainer('test');
			$this->getWebLoader()->createFilesCollectionsContainer('test');
		}, Exception::class, 'Files collections container "test" already exists.');
	}


	public function testDuplicatedCssFilterException(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()
				->addJsFilter('Lorem', function () {
					return '';
				})
				->addJsFilter('Lorem', function () {
					return '';
				});
		}, Exception::class, 'JS filter "Lorem" already exists.');
	}


	public function testDuplicatedJsFilterException(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()
				->addCssFilter('Lorem', function () {
					return '';
				})
				->addCssFilter('Lorem', function () {
					return '';
				});
		}, Exception::class, 'CSS filter "Lorem" already exists.');
	}


	public function testDuplicatedPlaceholderException(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()
				->addPathsPlaceholders([
					'frontCssDir' => __DIR__ . '/front/css',
				])
				->addPathsPlaceholders([
					'frontCssDir' => __DIR__ . '/front/css',
				]);
		}, Exception::class, 'Placeholder "frontCssDir" already exists.');
	}


	public function testFileNotFoundException(): void
	{
		Assert::exception(function () {
			$webLoader = $this->getWebLoader();
			$webLoader->createJsFilesCollection('test')->setFiles(['somefile.js']);
			$webLoader->getCompiler()->compileAllFilesCollections();
		}, Exception::class, 'File "somefile.js" not found.');
	}


	public function testMissingFilesCollectionsContainerConfigurationFileException(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()->createFilesCollectionsContainersFromConfig('path/to/config.neon');
		}, Exception::class, 'Files collections containers configuration file "path/to/config.neon" not found.');
	}


	public function testMissingFilesCollectionConfigurationFileException(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()->createFilesCollectionsFromConfig('path/to/config.neon');
		}, Exception::class, 'Files collections configuration file "path/to/config.neon" not found.');
	}


	public function testNullFilesCollectionsContainerException(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()->getFilesCollectionsContainerRender()->css();
		}, Exception::class, 'Trying to call files collections container render on NULL.');
	}


	public function testNullFilesCollectionException(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()->getFilesCollectionRender()->jsPrefetch();
		}, Exception::class, 'Trying to call files collection render on NULL.');
	}


	public function testUndefinedFilesCollectionsContainerException(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()->getFilesCollectionsContainerRender()->selectContainer('test')->css();
		}, Exception::class, 'Trying to get undefined files collections container "test".');
	}


	public function testUndefinedFilesCollectionException(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()->getFilesCollectionRender()->selectCollection('test')->css();
		}, Exception::class, 'Trying to get undefined CSS files collection "test".');
	}


	public function testUndefinedFilterException(): void
	{
		Assert::exception(function () {
			$webLoader = $this->getWebLoader();
			$webLoader->createJsFilesCollection('test')
					->setFiles(['%cssFixtures%/style-a.css'])
					->setFilters(['test']);
			$webLoader->getCompiler()->compileAllFilesCollections();
		}, Exception::class, 'Undefined JS filter "test".');
	}


	public function testUnknownFilesCollectionsContainerSection(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()
				->createFilesCollectionsContainersFromArray([
					'test' => [
						'csCollections' => [
							'collection'
						]
					]
				])
				->getFilesCollectionRender();
		}, Exception::class, 'Unknown configuration section "csCollections" in files collections container "test".');
	}


	public function testUnknownFilesCollectionSection(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()
				->createFilesCollectionsFromArray([
					'test' => [
						'csFilters' => [
							'file.css'
						]
					]
				])
				->getFilesCollectionRender();
		}, Exception::class, 'Unknown configuration section "csFilters" in files collection "test".');
	}


	public function testWrongDocumentRootException(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()->setDocumentRoot('path/to/some/dir');
		}, Exception::class, 'Given document root "path/to/some/dir" doesn\'t exists or is not a directory.');
	}


	public function testWrongOutputDirException(): void
	{
		Assert::exception(function () {
			$this->getWebLoader()->setOutputDir('path/to/some/dir');
		}, Exception::class, 'Given output dir "path/to/some/dir" doesn\'t exists or is not a directory.');
	}


	public function testRemoteFileCouldNotBeLoadedException(): void
	{
		Assert::exception(function (){
			$this->getWebLoader()
				->createFilesCollectionsFromConfig('%configsDir%/webloader.remote.collections.neon')
				->getFilesCollectionRender()
				->js('test-remote-files-loading');
		}, Exception::class, 'Remote file "https://example.com/xyz.js" could not be loaded.');
	}

}

(new ExceptionsTestCase())->run();
