from pyzbar.pyzbar import decode
from PIL import Image
import sys

image_path = sys.argv[1]
decoded_objects = decode(Image.open(image_path))

if decoded_objects:
    print(decoded_objects[0].data.decode('utf-8'))
else:
    print("false")
