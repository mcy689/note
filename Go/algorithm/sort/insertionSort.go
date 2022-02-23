package AlgoSort

/*
 * 插入排序：是稳定的排序算法，是原地排序算法。
 *  将待排序的数组划分为局部有序子数组subSorted和无序子数组subUnSorted，每次排序时从subUnSorted中挑出第一个元素，从后向前
 *  将其与subSorted各元素比较大小，按照大小插入合适的位置，插入完成后将此元素从subUnSorted中移除，重复这个过程直至subUnSorted中
 *  没有元素，总之就时从后向前，一边比较一边移动。
 */

func InsertionSort(nums []int, fn func(a, b int) bool) []int {
	count := len(nums)
	if count <= 1 {
		return nums
	}
	for i := 1; i < count; i++ {
		value := nums[i]
		j := i - 1
		for ; j >= 0; j-- {
			if fn(nums[j], value) {
				nums[j+1] = nums[j]
			} else {
				break
			}
		}
		nums[j+1] = value
	}
	return nums
}