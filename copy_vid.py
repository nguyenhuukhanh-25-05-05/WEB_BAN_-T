import shutil
import os

src = r"c:\Users\ADMIN\OneDrive\Ảnh\Downloads\98aa8604-a84e-4274-815b-b72ec8a2bbb2.mp4"
dst_dir = r"c:\Users\ADMIN\OneDrive\Ảnh\Downloads\WEB_DienThoai\assets\video"
dst = r"c:\Users\ADMIN\OneDrive\Ảnh\Downloads\WEB_DienThoai\assets\video\promo_video.mp4"

if not os.path.exists(dst_dir):
    os.makedirs(dst_dir)

if os.path.exists(src):
    shutil.copy2(src, dst)
    print("Done")
else:
    print("File not found")
