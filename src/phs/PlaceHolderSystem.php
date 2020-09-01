<?php
/*
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */


namespace smn\phs;

use ReflectionException;
use ReflectionFunction;

/**
 *
 * Class PlaceHolderSystem. La classe PlaceHolderSystem permette di creare dei pattern di stringhe con placeholder ai quali
 * saranno sostituiti i valori configurati.
 * I valori dei placeholder possono essere dichiarati sia in formato esplicito che sottoforma di callback
 * @package smn\phs
 */

class PlaceHolderSystem
{

    /**
     * Array multidipensionale dove l'indice è il nome di un placeholder, il valore è un array di parametri da passare
     * alla callback che si occupa di sostituire il placeholder
     * @var array|string[]
     */
    protected array $parameters = [];


    /**
     * Lista dei placeholder da utilizzare
     * @var array
     */
    protected array $placeholders = [];

    /**
     * Pattern da utilizzare
     * @var string
     */
    protected string $pattern;

    /**
     * Aggiunge un nuovo placeholder da tenere in considerazione per il rendering finale. Se $override è uguale a true e
     * $name già esiste, sarà generata una exception
     * @param string $name
     * @param mixed $value Può essere un valore o una callback. Alla callback è possibile passare tutti i valori che si desidera.
     * @param array $parameters
     * @throws PlaceHolderSystemException
     * @throws ReflectionException
     */
    public function addPlaceHolder(string $name, $value, array $parameters = []) {
            $this->placeholders[$name] = $value;
            if (is_callable($value)) {
                // se il placeholder è una call back, mi vado a vedere i parametri utilizzati e li mappo per $name
                $reflection = new ReflectionFunction($value);
                $count = count($reflection->getParameters());
                if ($count != count($parameters)) {
                    throw new PlaceHolderSystemException('Il numero dei parametri della callback non coincide con il numero dei parametri riportati');
                }

                $params = [];
                // raccolgo i parametri indicati nella callback, e antepongo un $ in quanto nella Clousure così vengono rappresentati i parametri
                foreach($reflection->getParameters() as $reflectionParameter) {
                    $params[] = sprintf('$%s', $reflectionParameter->getName());
                }
                // inverto chiavi con array, e poi scorro $params e uso ogni suo valore come chiave dei parametri passati in $parameters per mapparli
                $this->parameters[$name] = array_combine(array_values($params), array_values($parameters));

            }
    }

    /**
     * Verifica se un placeholder è configurato
     * @param string $name
     * @return bool
     */
    public function hasPlaceHolder(string $name) {
        return array_key_exists($name, $this->placeholders);
    }

    /**
     * Restituisce il valore di un placeholder. Se il placeholder non esiste restituisce false
     * @param string $name
     * @return bool
     */
    public function getPlaceHolder(string $name) {
        return ($this->hasPlaceHolder($name)) ? $this->placeholders[$name] : false;
    }

    /**
     * Rimuove un placeholder. Se il placeholder non esiste, lancia una exception
     * @param string $name
     * @throws PlaceHolderSystemException
     */

    public function removePlaceHolder(string $name) {
        if (!$this->hasPlaceHolder($name)) {
            throw new PlaceHolderSystemException(sprintf('Il placeholder %s non esiste', $name));
        }
        unset($this->placeholders[$name]);
    }

    /**
     * Restituisce il pattern configurato
     * @return string
     */
    public function getPattern() {
        return $this->pattern;
    }

    /**
     * Configura il pattern
     * @param string $pattern
     */
    public function setPattern(string $pattern) {
        $this->pattern = $pattern;
    }


    /**
     *
     */
    public function render() {

        $map = $this->placeholders;
        $string = $this->pattern;
        $pattern_regex = '/{([A-Za-z0-9\.\:_]+)+}/';
        $return = preg_replace_callback($pattern_regex, function($p) use ($map) {
            $matched = $p[1];
            $positions = array_flip(array_keys($map));
            if (array_key_exists($matched, $positions)) {
                $key = $positions[$matched];
                $key++;
                if (is_callable($map[$matched])) {
                    return call_user_func_array($map[$matched], $this->parameters[$matched]);
                }
                return sprintf('%%%s$s', $key);
            }
            return $p[0];

        }, $string);
        return vsprintf($return, $map);

    }

}