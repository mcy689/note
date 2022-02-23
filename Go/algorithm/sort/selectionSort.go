package AlgoSort

/*
 * 插入排序：是不稳定的排序算法，是原地排序算法。
 *  首先在未排序序列中找到最小（大）元素，存放到排序序列的起始位置，然后，再从剩余未排序元素中继续寻找
 *  最小（大）元素，然后放到已排序序列的末尾。以此类推，直到所有元素均排序完毕。
 */

func SelectionSort(nums []int, fn func(a, b int) bool) []int {
	count := len(nums)
	if count <= 1 {
		return nums
	}
	for i := 0; i < count; i++ {
		minIndex := i
		for j := 1 + i; j < count; j++ {
			if fn(nums[j], nums[minIndex]) {
				minIndex = j
			}
		}
		nums[i], nums[minIndex] = nums[minIndex], nums[i]
	}
	return nums
}
