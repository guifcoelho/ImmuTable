<?php

namespace guifcoelho\ImmuTable\Tests\Unit;

use guifcoelho\ImmuTable\Tests\TestCase;
use guifcoelho\ImmuTable\Tests\Support\TestingDatabase;
use guifcoelho\ImmuTable\Relations\Pivot;

use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample;
use guifcoelho\ImmuTable\Tests\Unit\SampleModels\Sample2;

class PivotTableTest extends TestCase
{
    use TestingDatabase;

    public function test_pivot_table(){
        $sample = ImmuTableFactory(Sample::class, $this->factory_path)->create();
        $sample2 = ImmuTableFactory(Sample2::class, $this->factory_path)->create();
        $relation = $sample->many_owners()->save($sample2);
        $table = Pivot::defineTable(class_basename($sample), class_basename($sample2));
        $this->assertDatabaseHas($table, [
            'sample_id' => $sample->id,
            'sample2_id' => $sample2->id
        ]);
    }

    public function test_pivot_table_with_different_name(){
        $sample = ImmuTableFactory(Sample::class, $this->factory_path)->create();
        $sample2 = ImmuTableFactory(Sample2::class, $this->factory_path)->create();
        $relation = $sample->many_owners_diffname()->save($sample2);
        $this->assertDatabaseHas($relation->getPivot()->getTable(), [
            'sample_id' => $sample->id,
            'sample2_id' => $sample2->id
        ]);
    }

    public function test_pivot_table_with_different_name_and_diff_field_names(){
        $sample = ImmuTableFactory(Sample::class, $this->factory_path)->create();
        $sample2 = ImmuTableFactory(Sample2::class, $this->factory_path)->create();
        
        $relation = $sample->many_owners_diffname_difffield()->save($sample2);
        $this->assertDatabaseHas($relation->getPivot()->getTable(), [
            'child' => $sample->id,
            'sample2_id' => $sample2->id
        ]);

        $relation = $sample->many_owners_diffname_difffield_diffparentfield()->save($sample2);
        $this->assertDatabaseHas($relation->getPivot()->getTable(), [
            'child' => $sample->id,
            'parent' => $sample2->id
        ]);

        $relation = $sample->many_owners_diffname_difffield_diffparentfield()->save($sample2);
        $this->assertDatabaseHas($relation->getPivot()->getTable(), [
            'child' => $sample->id,
            'parent' => $sample2->id
        ]);

        $relation = $sample->many_owners_diffname_diff_model_field()->save($sample2);
        $this->assertDatabaseHas($relation->getPivot()->getTable(), [
            'email' => $sample->email,
            'sample2_id' => $sample2->id
        ]);

        $relation = $sample->many_owners_diffname_diff_model_field_and_diff_parent_model_field()->save($sample2);
        $this->assertDatabaseHas($relation->getPivot()->getTable(), [
            'sample_email' => $sample->email,
            'sample2_email' => $sample2->email
        ]);
    }

}
