package AlgoSort

/**
  计数排序
    当要排序的n个数据，所处的范围并不大的时候
*/

func CountingSort(nums []int) []int {
	count := len(nums)
	if count == 1 {
		return nums
	}
	// 1.查找数据的范围
	var maxVal int = nums[0]
	for i := 1; i < count; i++ {
		if nums[i] > maxVal {
			maxVal = nums[i]
		}
	}
	// 2.计数切片，从0开始的所以需要加1
	var cArr = make([]int, maxVal+1)
	// 3.计算每个元素的个数，放入 cArr
	for i := 0; i < count; i++ {
		//转换索引
		cArr[nums[i]]++
	}
	// 4.将计数切片依次累加
	for i := 1; i <= maxVal; i++ {
		cArr[i] = cArr[i] + cArr[i-1]
	}
	// 5.存储临时排序结果
	tmp := make([]int, count)
	for i := 0; i < count; i++ {
		index := cArr[nums[i]] - 1
		tmp[index] = nums[i]
		cArr[nums[i]]--
	}
	copy(nums, tmp)
	return nums
}
