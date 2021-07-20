package leetcode

import (
	"reflect"
	"testing"
)

func twoSumFor(nums []int, target int) []int {
	m := make(map[int]int)
	le := len(nums)
	for i := 0; i < le; i++ {
		another := target - nums[i]
		if _, ok := m[another]; ok {
			return []int{m[another], i}
		}
		m[nums[i]] = i
	}
	return nil
}

func twoSumRange(nums []int, target int) []int {
	var result []int
	old := make(map[int]int)
	for key, value := range nums {
		if search, exist := old[target-value]; exist {
			result = append(result, search)
			result = append(result, key)
		}
		old[value] = key
	}
	return result
}

func TestTwoSum(t *testing.T) {
	ok := reflect.DeepEqual(twoSumFor([]int{2, 7, 11, 15}, 9), []int{0, 1})
	ok = ok && reflect.DeepEqual(twoSumFor([]int{3, 2, 4}, 6), []int{1, 2})
	ok = ok && reflect.DeepEqual(twoSumFor([]int{3, 3}, 6), []int{0, 1})
	if !ok {
		t.Fatal("test twoSumRange failed")
	}

	okR := reflect.DeepEqual(twoSumRange([]int{2, 7, 11, 15}, 9), []int{0, 1})
	okR = okR && reflect.DeepEqual(twoSumRange([]int{3, 2, 4}, 6), []int{1, 2})
	okR = okR && reflect.DeepEqual(twoSumRange([]int{3, 3}, 6), []int{0, 1})
	if !okR {
		t.Fatal("test twoSumFor failed")
	}
}

func BenchmarkTwoSum(b *testing.B) {
	nums := []int{2, 7, 11, 15}
	target := 9
	for i := 0; i < b.N; i++ {
		twoSumFor(nums, target)
	}
}

func BenchmarkTwoSumRange(b *testing.B) {
	nums := []int{2, 7, 11, 15}
	target := 9
	for i := 0; i < b.N; i++ {
		twoSumRange(nums, target)
	}
}
