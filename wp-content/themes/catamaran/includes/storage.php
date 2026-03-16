<?php
/**
 * Theme storage manipulations
 *
 * @package CATAMARAN
 * @since CATAMARAN 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'catamaran_storage_get' ) ) {
	/**
	 * Return a value of the specified variable from the global theme storage.
	 *
	 * @param string $var_name  A name of the variable to get its value.
	 * @param mixed $default    Optional. A default value, used if the specified variable is not found
	 *                          in the global theme storage. If omitted - empty string is used as the default value.
	 *
	 * @return mixed            A value from the theme global storage or the default value.
	 */
	function catamaran_storage_get( $var_name, $default = '' ) {
		global $CATAMARAN_STORAGE;
		return isset( $CATAMARAN_STORAGE[ $var_name ] ) ? $CATAMARAN_STORAGE[ $var_name ] : $default;
	}
}

if ( ! function_exists( 'catamaran_storage_set' ) ) {
	/**
	 * Set (update) a value of the specified variable in the global theme storage.
	 *
	 * @param string $var_name  A name of the variable to set (update) its value.
	 * @param mixed $value      A new value.
	 */
	function catamaran_storage_set( $var_name, $value ) {
		global $CATAMARAN_STORAGE;
		$CATAMARAN_STORAGE[ $var_name ] = $value;
	}
}

if ( ! function_exists( 'catamaran_storage_empty' ) ) {
	/**
	 * Check if a specified variable or an array key is empty (not exists or have an empty value) in the global theme storage.
	 *
	 * @param string $var_name  A name of the variable to check.
	 * @param string $key       Optional. A first array key. If specified - a ${$var_name}[$key] will be checked for empty.
	 * @param string $key2      Optional. A second array key. If specified - a ${$var_name}[$key][$key2] will be checked for empty.
	 *
	 * @return bool             Return true if the specified variable or array cell is not exists or contain an empty value.
	 */
	function catamaran_storage_empty( $var_name, $key = '', $key2 = '' ) {
		global $CATAMARAN_STORAGE;
		if ( '' !== $key && '' !== $key2 ) {
			return empty( $CATAMARAN_STORAGE[ $var_name ][ $key ][ $key2 ] );
		} elseif ( '' !== $key ) {
			return empty( $CATAMARAN_STORAGE[ $var_name ][ $key ] );
		} else {
			return empty( $CATAMARAN_STORAGE[ $var_name ] );
		}
	}
}

if ( ! function_exists( 'catamaran_storage_isset' ) ) {
	/**
	 * Check if a specified variable or an array key is set (exists) in the global theme storage.
	 *
	 * @param string $var_name  A name of the variable to check.
	 * @param string $key       Optional. A first array key. If specified - a ${$var_name}[$key] will be checked for exists.
	 * @param string $key2      Optional. A second array key. If specified - a ${$var_name}[$key][$key2] will be checked for exists.
	 *
	 * @return bool             Return true if the specified variable or array cell is set (exists).
	 */
	function catamaran_storage_isset( $var_name, $key = '', $key2 = '' ) {
		global $CATAMARAN_STORAGE;
		if ( '' !== $key && '' !== $key2 ) {
			return isset( $CATAMARAN_STORAGE[ $var_name ][ $key ][ $key2 ] );
		} elseif ( '' !== $key ) {
			return isset( $CATAMARAN_STORAGE[ $var_name ][ $key ] );
		} else {
			return isset( $CATAMARAN_STORAGE[ $var_name ] );
		}
	}
}

if ( ! function_exists( 'catamaran_storage_unset' ) ) {
	/**
	 * Delete the specified variable or an array key from the global theme storage.
	 *
	 * @param string $var_name  A name of the variable to delete.
	 * @param string $key       Optional. A first array key. If specified - a ${$var_name}[$key] will be deleted.
	 * @param string $key2      Optional. A second array key. If specified - a ${$var_name}[$key][$key2] will be deleted.
	 */
	function catamaran_storage_unset( $var_name, $key = '', $key2 = '' ) {
		global $CATAMARAN_STORAGE;
		if ( '' !== $key && '' !== $key2 ) {
			if ( isset( $CATAMARAN_STORAGE[ $var_name ][ $key ][ $key2 ] ) ) {
				unset( $CATAMARAN_STORAGE[ $var_name ][ $key ][ $key2 ] );
			}
		} elseif ( '' !== $key ) {
			if ( isset( $CATAMARAN_STORAGE[ $var_name ][ $key ] ) ) {
				unset( $CATAMARAN_STORAGE[ $var_name ][ $key ] );
			}
		} else {
			if ( isset( $CATAMARAN_STORAGE[ $var_name ] ) ) {
				unset( $CATAMARAN_STORAGE[ $var_name ] );
			}
		}
	}
}

if ( ! function_exists( 'catamaran_storage_inc' ) ) {
	/**
	 * Increment the specified variable in the global theme storage.
	 *
	 * @param string $var_name  A name of the variable to increment.
	 * @param int $value        Optional. A value for increment. Default is 1. Specify -1 to decrement or any legal number value.
	 */
	function catamaran_storage_inc( $var_name, $value = 1 ) {
		global $CATAMARAN_STORAGE;
		if ( empty( $CATAMARAN_STORAGE[ $var_name ] ) ) {
			$CATAMARAN_STORAGE[ $var_name ] = 0;
		}
		$CATAMARAN_STORAGE[ $var_name ] += $value;
	}
}

if ( ! function_exists( 'catamaran_storage_concat' ) ) {
	/**
	 * Concatenate the specified variable in the global theme storage with the new value.
	 *
	 * @param string $var_name  A name of the variable to concatenate.
	 * @param int $value        A value to concatenate.
	 */
	function catamaran_storage_concat( $var_name, $value ) {
		global $CATAMARAN_STORAGE;
		if ( empty( $CATAMARAN_STORAGE[ $var_name ] ) ) {
			$CATAMARAN_STORAGE[ $var_name ] = '';
		}
		$CATAMARAN_STORAGE[ $var_name ] .= $value;
	}
}

if ( ! function_exists( 'catamaran_storage_get_array' ) ) {
	/**
	 * Get a value from an array (single or two dimensional) from the global theme storage.
	 *
	 * @param string $var_name  A name of the array.
	 * @param string $key       A first array key: ${$var_name}[$key] will be returned.
	 * @param string $key2      Optional. A second array key. If specified - a ${$var_name}[$key][$key2] will be returned.
	 * @param mixed  $default   Optional. A default value to return if a queried element is not exists. If omitted - an empty string is used.
	 *
	 * @return mixed            An array element value or a default value (if a queried element is not exists in the array).
	 */
	function catamaran_storage_get_array( $var_name, $key, $key2 = '', $default = '' ) {
		global $CATAMARAN_STORAGE;
		if ( '' === $key2 ) {
			return ! empty( $var_name ) && '' !== $key && isset( $CATAMARAN_STORAGE[ $var_name ][ $key ] ) ? $CATAMARAN_STORAGE[ $var_name ][ $key ] : $default;
		} else {
			return ! empty( $var_name ) && '' !== $key && isset( $CATAMARAN_STORAGE[ $var_name ][ $key ][ $key2 ] ) ? $CATAMARAN_STORAGE[ $var_name ][ $key ][ $key2 ] : $default;
		}
	}
}

if ( ! function_exists( 'catamaran_storage_set_array' ) ) {
	/**
	 * Set a new value in the array in the global theme storage.
	 *
	 * @param string $var_name  A name of the array.
	 * @param string $key       An array key: If is empty string - an array element ${$var_name}[] will be added,
	 *                          else - an array element ${$var_name}[$key] will be set (updated).
	 * @param mixed  $value     A new value for the specified element of the array.
	 */
	function catamaran_storage_set_array( $var_name, $key, $value ) {
		global $CATAMARAN_STORAGE;
		if ( ! isset( $CATAMARAN_STORAGE[ $var_name ] ) ) {
			$CATAMARAN_STORAGE[ $var_name ] = array();
		}
		if ( is_array( $CATAMARAN_STORAGE[ $var_name ] ) ) {
			if ( '' === $key ) {
				$CATAMARAN_STORAGE[ $var_name ][] = $value;
			} else {
				$CATAMARAN_STORAGE[ $var_name ][ $key ] = $value;
			}
		}
	}
}

if ( ! function_exists( 'catamaran_storage_set_array2' ) ) {
	/**
	 * Set a new value in the two-dimensional array in the global theme storage.
	 *
	 * @param string $var_name  A name of the array.
	 * @param string $key       A first key of the array.
	 * @param string $key2      A second key of the array: ${$var_name}[$key][$key2] will be updated.
	 * @param mixed  $value     A new value for the specified element of the array.
	 */
	function catamaran_storage_set_array2( $var_name, $key, $key2, $value ) {
		global $CATAMARAN_STORAGE;
		if ( ! isset( $CATAMARAN_STORAGE[ $var_name ] ) ) {
			$CATAMARAN_STORAGE[ $var_name ] = array();
		}
		if ( ! isset( $CATAMARAN_STORAGE[ $var_name ][ $key ] ) ) {
			$CATAMARAN_STORAGE[ $var_name ][ $key ] = array();
		}
		if ( is_array( $CATAMARAN_STORAGE[ $var_name ][ $key ] ) ) {
			if ( '' === $key2 ) {
				$CATAMARAN_STORAGE[ $var_name ][ $key ][] = $value;
			} else {
				$CATAMARAN_STORAGE[ $var_name ][ $key ][ $key2 ] = $value;
			}
		}
	}
}

if ( ! function_exists( 'catamaran_storage_merge_array' ) ) {
	/**
	 * Merge new elements to the array in the global theme storage.
	 *
	 * @param string $var_name  A name of the array.
	 * @param string $key       An array key. If is empty string - an array ${$var_name} will be merged,
	 *                          else - an array ${$var_name}[$key] will be merged.
	 * @param array  $value     An array with elements to merge.
	 */
	function catamaran_storage_merge_array( $var_name, $key, $value ) {
		global $CATAMARAN_STORAGE;
		if ( ! isset( $CATAMARAN_STORAGE[ $var_name ] ) ) {
			$CATAMARAN_STORAGE[ $var_name ] = array();
		}
		if ( '' === $key ) {
			$CATAMARAN_STORAGE[ $var_name ] = array_merge( $CATAMARAN_STORAGE[ $var_name ], $value );
		} else {
			$CATAMARAN_STORAGE[ $var_name ][ $key ] = array_merge( $CATAMARAN_STORAGE[ $var_name ][ $key ], $value );
		}
	}
}

if ( ! function_exists( 'catamaran_storage_set_array_after' ) ) {
	/**
	 * Insert new elements to the array in the global theme storage
	 * after the specified key.
	 *
	 * @param string       $var_name  A name of the array.
	 * @param string       $after     An array key to insert new elements after it.
	 * @param string|array $key       A key of a new element or an array with new elements.
	 * @param mixed        $value     A new value to insert (if the argument $key is a string).
	 */
	function catamaran_storage_set_array_after( $var_name, $after, $key, $value = '' ) {
		global $CATAMARAN_STORAGE;
		if ( ! isset( $CATAMARAN_STORAGE[ $var_name ] ) ) {
			$CATAMARAN_STORAGE[ $var_name ] = array();
		}
		if ( is_array( $key ) ) {
			catamaran_array_insert_after( $CATAMARAN_STORAGE[ $var_name ], $after, $key );
		} else {
			catamaran_array_insert_after( $CATAMARAN_STORAGE[ $var_name ], $after, array( $key => $value ) );
		}
	}
}

if ( ! function_exists( 'catamaran_storage_set_array_before' ) ) {
	/**
	 * Insert new elements to the array in the global theme storage
	 * before the specified key.
	 *
	 * @param string       $var_name  A name of the array.
	 * @param string       $before    An array key to insert new elements before it.
	 * @param string|array $key       A key of a new element or an array with new elements.
	 * @param mixed        $value     A new value to insert (if the argument $key is a string).
	 */
	function catamaran_storage_set_array_before( $var_name, $before, $key, $value = '' ) {
		global $CATAMARAN_STORAGE;
		if ( ! isset( $CATAMARAN_STORAGE[ $var_name ] ) ) {
			$CATAMARAN_STORAGE[ $var_name ] = array();
		}
		if ( is_array( $key ) ) {
			catamaran_array_insert_before( $CATAMARAN_STORAGE[ $var_name ], $before, $key );
		} else {
			catamaran_array_insert_before( $CATAMARAN_STORAGE[ $var_name ], $before, array( $key => $value ) );
		}
	}
}

if ( ! function_exists( 'catamaran_storage_push_array' ) ) {
	/**
	 * Push a new element to the end of array or subarray (if argument $key is not empty).
	 *
	 * @param string       $var_name  A name of the array.
	 * @param string|array $key       A key of a subarray ${$var_name}[$key] to push a new element.
	 *                                If empty - a new element will be pushed to the array ${$var_name}
	 * @param mixed        $value     A new value to push to the (sub)array.
	 */
	function catamaran_storage_push_array( $var_name, $key, $value ) {
		global $CATAMARAN_STORAGE;
		if ( ! isset( $CATAMARAN_STORAGE[ $var_name ] ) ) {
			$CATAMARAN_STORAGE[ $var_name ] = array();
		}
		if ( '' === $key ) {
			array_push( $CATAMARAN_STORAGE[ $var_name ], $value );
		} else {
			if ( ! isset( $CATAMARAN_STORAGE[ $var_name ][ $key ] ) ) {
				$CATAMARAN_STORAGE[ $var_name ][ $key ] = array();
			}
			array_push( $CATAMARAN_STORAGE[ $var_name ][ $key ], $value );
		}
	}
}

if ( ! function_exists( 'catamaran_storage_pop_array' ) ) {
	/**
	 * Pop a last element from the array or subarray (if argument $key is not empty).
	 *
	 * @param string       $var_name  A name of the array.
	 * @param string|array $key       Optional. A key of a subarray ${$var_name}[$key] to pop a last element.
	 *                                If empty - a last element from the array ${$var_name} will be popped.
	 * @param mixed        $defa      A default value (return it if queried element is not found).
	 *
	 * @return mixed                  A last element from the (sub)array.
	 */
	function catamaran_storage_pop_array( $var_name, $key = '', $defa = '' ) {
		global $CATAMARAN_STORAGE;
		$rez = $defa;
		if ( '' === $key ) {
			if ( isset( $CATAMARAN_STORAGE[ $var_name ] ) && is_array( $CATAMARAN_STORAGE[ $var_name ] ) && count( $CATAMARAN_STORAGE[ $var_name ] ) > 0 ) {
				$rez = array_pop( $CATAMARAN_STORAGE[ $var_name ] );
			}
		} else {
			if ( isset( $CATAMARAN_STORAGE[ $var_name ][ $key ] ) && is_array( $CATAMARAN_STORAGE[ $var_name ][ $key ] ) && count( $CATAMARAN_STORAGE[ $var_name ][ $key ] ) > 0 ) {
				$rez = array_pop( $CATAMARAN_STORAGE[ $var_name ][ $key ] );
			}
		}
		return $rez;
	}
}

if ( ! function_exists( 'catamaran_storage_inc_array' ) ) {
	/**
	 * Increment/Decrement the specified element of the array in the global theme storage.
	 *
	 * @param string       $var_name  A name of the array.
	 * @param string|array $key       A key of an array ${$var_name}[$key] to modify value.
	 * @param int          $value     Optional. A value for increment. Default is 1. Specify -1 to decrement or any legal number value.
	 */
	function catamaran_storage_inc_array( $var_name, $key, $value = 1 ) {
		global $CATAMARAN_STORAGE;
		if ( ! isset( $CATAMARAN_STORAGE[ $var_name ] ) ) {
			$CATAMARAN_STORAGE[ $var_name ] = array();
		}
		if ( empty( $CATAMARAN_STORAGE[ $var_name ][ $key ] ) ) {
			$CATAMARAN_STORAGE[ $var_name ][ $key ] = 0;
		}
		$CATAMARAN_STORAGE[ $var_name ][ $key ] += $value;
	}
}

if ( ! function_exists( 'catamaran_storage_concat_array' ) ) {
	/**
	 * Concatenate the specified element of the array with a new value in the global theme storage.
	 *
	 * @param string       $var_name  A name of the array.
	 * @param string|array $key       A key of an array ${$var_name}[$key] to concatenate with a new value.
	 * @param int          $value     A value to concatenate.
	 */
	function catamaran_storage_concat_array( $var_name, $key, $value ) {
		global $CATAMARAN_STORAGE;
		if ( ! isset( $CATAMARAN_STORAGE[ $var_name ] ) ) {
			$CATAMARAN_STORAGE[ $var_name ] = array();
		}
		if ( empty( $CATAMARAN_STORAGE[ $var_name ][ $key ] ) ) {
			$CATAMARAN_STORAGE[ $var_name ][ $key ] = '';
		}
		$CATAMARAN_STORAGE[ $var_name ][ $key ] .= $value;
	}
}

if ( ! function_exists( 'catamaran_storage_call_obj_method' ) ) {
	/**
	 * Call a specified method of the object from the global theme storage.
	 *
	 * @param string     $var_name  A name of the object.
	 * @param string     $method    A name of the method.
	 * @param midex|null $param     A parameter to pass to the method.
	 *
	 * @return mixed                Return a method result.
	 */
	function catamaran_storage_call_obj_method( $var_name, $method, $param = null ) {
		global $CATAMARAN_STORAGE;
		if ( null === $param ) {
			return ! empty( $var_name ) && ! empty( $method ) && isset( $CATAMARAN_STORAGE[ $var_name ] ) ? $CATAMARAN_STORAGE[ $var_name ]->$method() : '';
		} else {
			return ! empty( $var_name ) && ! empty( $method ) && isset( $CATAMARAN_STORAGE[ $var_name ] ) ? $CATAMARAN_STORAGE[ $var_name ]->$method( $param ) : '';
		}
	}
}

if ( ! function_exists( 'catamaran_storage_get_obj_property' ) ) {
	/**
	 * Get a property value of the object from the global theme storage.
	 *
	 * @param string     $var_name  A name of the object.
	 * @param string     $prop      A name of the property.
	 * @param midex|null $default   Optional. A default value to return if a property is not exists.
	 *
	 * @return mixed                A value of the specified property.
	 */
	function catamaran_storage_get_obj_property( $var_name, $prop, $default = '' ) {
		global $CATAMARAN_STORAGE;
		return ! empty( $var_name ) && ! empty( $prop ) && isset( $CATAMARAN_STORAGE[ $var_name ]->$prop ) ? $CATAMARAN_STORAGE[ $var_name ]->$prop : $default;
	}
}
