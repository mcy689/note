## 两数之和

### golang

```go
package main

import (
	"fmt"
)

func main() {
	var nums = []int{3,2,4}
	fmt.Printf("%#v", twoSum(nums, 6)) //[1,2]
}

func twoSum(nums []int, target int) []int {
	var result []int
	var oldMap = make(map[int]int)
	for key, value := range nums {
		if search, exist := oldMap[target-value]; exist {
			result = append(result, search)
			result = append(result, key)
		}
		oldMap[value] = key
	}
	return result
}
```

### php

```php
var_dump(twoSum([3,2,4]),6); // [1,2]

function twoSum($nums, $target) {
  foreach($nums as $key => $item) {
    $resKey = array_search($target-$item,$nums);
    if ($resKey !== false && $key !== $resKey) {
      return [$key,$resKey];
    }
  }
  return [];
}
```

