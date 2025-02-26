<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Task;

/**
 * TaskSearch represents the model behind the search form of `common\models\Task`.
 */
class TaskSearch extends Task
{
    /**
     * {@inheritdoc}
     */

     public $author;
     public $customer;
    public function rules()
    {
        return [
            [['id', 'status', 'prioritet', 'user_id', 'readliness'], 'integer'],
            [['name', 'date_add', 'date_end', 'text', 'ocenka_truda', 'author', 'author_id', 'customer'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $user_id = false)
    {

        $query = Task::find()->joinWith(@author)->joinWith(@customer);

        // add conditions that should always apply here
     
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
               ]
        ]);
        // Сортировка по связной таблице
        $dataProvider->sort->attributes['author'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['user.username' => SORT_ASC],
            'desc' => ['user.username' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['customer'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['user.username' => SORT_ASC],
            'desc' => ['user.username' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'task.status' => $this->status,
            'prioritet' => $this->prioritet,
            'date_add' => $this->date_add,
            'date_end' => $this->date_end,
            'user_id' => $this->user_id,
            'readliness' => $this->readliness,
        ]);
        if($user_id) {
            $query->andFilterWhere([
                'user_id' => $user_id, 
            ]);
        }
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'user.username', $this->author])
            ->andFilterWhere(['like', 'user.username', $this->customer])
            ->andFilterWhere(['like', 'ocenka_truda', $this->ocenka_truda]);

        return $dataProvider;
    }
   
}
