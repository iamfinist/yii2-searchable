<?php
/**
 * @link https://github.com/vuongxuongminh/yii2-searchable
 * @copyright Copyright (c) 2019 Vuong Xuong Minh
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace vxm\test\unit\searchable;

use Yii;

use yii\helpers\ArrayHelper;

/**
 * Class SearchableBehaviorTest
 *
 * @author Vuong Minh <vuongxuongminh@gmail.com>
 * @since 1.0.0
 */
class SearchableBehaviorTest extends TestCase
{

    public function testSync()
    {
        $model = Model::findOne(1);
        $model->article = 'testSync';
        $model->save(false);

        $models = Model::searchableSearch('testSync')->all();
        $this->assertEquals(1, count($models));
    }

    public function testUnSync()
    {
        Model::withoutSyncingToSearch(function () {
            $model = Model::findOne(1);
            $model->article = 'testUnSync';
            $model->save(false);

            $models = Model::searchableSearch('testUnSync')->all();
            $this->assertEquals(0, count($models));
        });
    }

    public function testShouldBeSearchable()
    {
        $model = Model::findOne(1);
        $model->shouldBeSearchable = false;
        $model->article = 'testShouldBeSearchable';
        $model->save(false);

        $models = Model::searchableSearch('testShouldBeSearchable')->all();
        $this->assertEquals(0, count($models));
    }

    public function testDeleteSync()
    {
        $model = new Model([
            'title' => 'testDeleteSync title',
            'article' => 'testDeleteSync article'
        ]);

        $model->save(false);
        $modelSearch = Model::searchableSearch('testDeleteSync title')->one();
        $this->assertNotNull($modelSearch);
        $modelSearch->delete();
        $modelSearch = Model::searchableSearch('testDeleteSync title')->one();
        $this->assertNull($modelSearch);
    }

    public function testDeleteUnSync()
    {
        $model = new Model([
            'title' => 'testDeleteUnSync title',
            'article' => 'testDeleteUnSync article'
        ]);

        $model->save(false);
        $modelSearch = Model::searchableSearch('testDeleteUnSync title')->one();
        $this->assertNotNull($modelSearch);

        Model::withoutSyncingToSearch(function () use ($model) {
            $model->delete();
            $modelSearch = Model::searchIds('testDeleteUnSync title');
            $this->assertNotEmpty($modelSearch);
        });
    }

    public function testOrderBy()
    {
        Model::makeAllSearchable();
        $ids = Model::searchIds('Romeo');
        $models = Model::searchableSearch('Romeo')->all();
        $modelIds = ArrayHelper::getColumn($models, 'id');

        $this->assertEquals($ids, $modelIds);

        $models = Model::searchableSearch('Romeo')->addOrderBy(['article' => SORT_DESC])->all();
        $modelIds = ArrayHelper::getColumn($models, 'id');

        $this->assertNotEquals($ids, $modelIds);
    }
}
