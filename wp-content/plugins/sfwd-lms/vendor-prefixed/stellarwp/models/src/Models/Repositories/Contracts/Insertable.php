<?php
/**
 * @license GPL-3.0-or-later
 *
 * Modified by learndash on 09-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\Learndash\StellarWP\Models\Repositories\Contracts;

use StellarWP\Learndash\StellarWP\Models\Contracts\Model;

interface Insertable {
	/**
	 * Inserts a model record.
	 *
	 * @since 1.0.0
	 *
	 * @param Model $model
	 *
	 * @return Model
	 */
	public function insert( Model $model ) : Model;
}
