#!/bin/bash
profile_path="storage/app/public/profile_images"
test_path="tests/test_images/profile_images"

for test in AddressTest.php CommentTest.php ExampleTest.php ItemListTest.php ItemRegisterTest.php ItemTest.php LikeTest.php LoginTest.php LogoutTest.php MyListTest.php ProfileEditTest.php ProfileTest.php PurchaseTest.php RegisterTest.php SearchTest.php
do
    php artisan test "tests/Feature/$test"

    # プロフィールのテスト画像は削除される可能性があるので都度復元する
    cp -pf "$test_path"/* "$profile_path"
    if [ $? -ne 0 ]; then
        echo "=================================================="
        echo "プロフィール画像の復元処理（copy）に失敗しました"
        echo "処理を終了します"
        echo "=================================================="
        exit 1
    fi
done