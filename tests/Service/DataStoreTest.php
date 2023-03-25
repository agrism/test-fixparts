<?php

namespace App\Tests\Service;

use App\Service\DataStore;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DataStoreTest extends TestCase
{
    const STORE_PATH = __DIR__ . '/test-file.data';

    public function testInstanceDataStore(): void
    {
        $service = new DataStore(self::STORE_PATH);
        $this->assertInstanceOf(DataStore::class, $service);
    }

    public function testSetAndGetDataDataStore(): void
    {
        $service = new DataStore(self::STORE_PATH);

        foreach (self::valuesProvider() as $data) {
            list ($item, $primary, $value) = $data;

            $service->set($item, $primary, $value);
            $saved = $service->get($item, $primary);

            $this->assertSame($value, $saved);
        }
    }

    #[DataProvider('valuesProvider')]
    public function testDeleteDataStore($item, $primary, $value): void
    {
        $service = new DataStore(self::STORE_PATH);

        $service->set($item, $primary, $value);
        $data = $service->get($item, $primary);
        $this->assertSame($value, $data);

        $service->delete($item, $primary);
        $data = $service->get($item, $primary);
        $this->assertNull($data);
    }

    public function testGetItemTypesDataStore(): void
    {
        $service = new DataStore(self::STORE_PATH);

        $values = self::getValues();
        $expected = [];

        foreach ($values as $data) {
            list ($item, $primary, $value) = $data;
            $expected[$item][$primary] = $value;
            $service->set($item, $primary, $value);
        }

        $itemTypes = $service->getItemTypes();

        $this->assertEquals(array_keys($expected), $itemTypes);
    }

    public function testGetItemTypesNullDataStore(): void
    {
        $service = new DataStore(self::STORE_PATH);

        $itemTypes = $service->getItemTypes();
        $this->assertEquals([], $itemTypes);
    }

    public function testGetItemKeysDataStore(): void
    {
        $service = new DataStore(self::STORE_PATH);

        foreach (self::getValuesWithKeys() as $data) {
            list($item, $primary, $value, $expected) = $data;

            $service->set($item, $primary, $value);

            $keys = $service->getItemKeys($item);
            $this->assertSame($expected, $keys);
        }
    }

    public function testSaveDataStore(): void
    {
        $serviceInstance1 = new DataStore(self::STORE_PATH);

        $values = self::getValues();
        $expected = [];

        foreach ($values as $data) {
            list ($item, $primary, $value) = $data;
            $expected[$item][$primary] = $value;
            $serviceInstance1->set($item, $primary, $value);
        }

        $serviceInstance1->save();
        unset($serviceInstance1);

        $serviceInstance2 = new DataStore(self::STORE_PATH);
        $data =$serviceInstance2->getAll();

        $this->assertEquals($expected, $data);
    }

    public static function valuesProvider(): array
    {
        return self::getValues();
    }

    private static function getValues(): array
    {
        return [
            ['test1', 'property1', 'value1'],
            ['test1', 'property2', 'value2'],
            ['test2', 'property1', 100],
            ['test2', 'property2', null],
        ];
    }

    private static function getValuesWithKeys(): array
    {
        return [
            ['test1', 'property1', 'value1', ['property1']],
            ['test1', 'property2', 'value2', ['property1', 'property2']],
            ['test1', 'property3', 100, ['property1', 'property2', 'property3']],
            ['test2', 'property1', -100, ['property1']],
            ['test2', 'property2', false, ['property1', 'property2']],
        ];

    }

    protected function tearDown(): void
    {
        if (is_file(self::STORE_PATH)) {
            unlink(self::STORE_PATH);
        }
    }
}