package algorithm

import (
	"container/list"
	"sync"
)

type Queue struct {
	data *list.List
	lock sync.Mutex
}

func NewQueue() *Queue {
	return &Queue{list.New(), sync.Mutex{}}
}

func (q *Queue) Push(v interface{}) {
	defer q.lock.Unlock()
	q.lock.Lock()
	q.data.PushBack(v)
}

func (q *Queue) Pop() interface{} {
	defer q.lock.Unlock()
	q.lock.Lock()
	item := q.data.Front()
	v := item.Value
	q.data.Remove(item)
	return v
}
