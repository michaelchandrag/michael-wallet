<?php

namespace Repository\Contract;

interface CategoryContract {
	
	public function find($filter);
	public function findOne($filter);
	public function create($data);
	public function modify($filter, $data);

}