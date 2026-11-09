<?php
declare(strict_types=1);

namespace Magento\Framework\Component {
    if (!class_exists(ComponentRegistrar::class, false)) {
        class ComponentRegistrar
        {
            public const MODULE = 'module';
            public static function register(string $type, string $componentName, string $path): void {}
        }
    }
}

namespace Magento\Framework\Data {
    if (!interface_exists(OptionSourceInterface::class, false)) {
        interface OptionSourceInterface
        {
            public function toOptionArray(): array;
        }
    }
}

namespace Magento\Framework {
    if (!class_exists(DataObject::class, false)) {
        class DataObject
        {
            protected array $_data = [];
            public function __construct(array $data = []) { $this->_data = $data; }
            public function getData(?string $key = null) { return $key === null ? $this->_data : ($this->_data[$key] ?? null); }
            public function setData(string $key, $value = null) { $this->_data[$key] = $value; return $this; }
        }
    }
}

namespace Magento\Framework\Api {
    if (!class_exists(AbstractSimpleObject::class, false)) {
        abstract class AbstractSimpleObject
        {
            protected array $_data = [];
            public function __construct(array $data = []) { $this->_data = $data; }
            public function _get(string $key) { return $this->_data[$key] ?? null; }
            public function _set(string $key, $value) { $this->_data[$key] = $value; return $this; }
            public function getData(?string $key = null) { return $key === null ? $this->_data : ($this->_data[$key] ?? null); }
            public function setData(string $key, $value = null) { $this->_data[$key] = $value; return $this; }
            public function __getData() { return $this->_data; }
        }
    }
}

namespace {
    if (!function_exists('__')) {
        function __($text, ...$args) {
            return (string)$text;
        }
    }
}

namespace {
    spl_autoload_register(function ($class) {
        $prefix = "PriyankJivani\\CacheWarmerCli\\";
        if (strpos($class, $prefix) === 0) {
            $relative = substr($class, strlen($prefix));
            $file = __DIR__ . '/../' . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    });
}
