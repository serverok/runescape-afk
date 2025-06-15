#!/home/boby/venv/bin/python

import cv2
import numpy as np
import sys

def template_matching(image_path, template_path):
    # Load the image and the template
    image = cv2.imread(image_path)
    template = cv2.imread(template_path)

    # Convert to grayscale for processing
    image_gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    template_gray = cv2.cvtColor(template, cv2.COLOR_BGR2GRAY)

    # Perform template matching
    result = cv2.matchTemplate(image_gray, template_gray, cv2.TM_CCOEFF_NORMED)

    # Get the maximum value and its location from the result
    min_val, max_val, min_loc, max_loc = cv2.minMaxLoc(result)

    # Set a threshold for the match. For example, 0.8 for 80% match.
    threshold = 0.6

    print("min_val = ", min_val)
    print("max_val = ", max_val)
    print("min_loc = ", min_loc)
    print("max_loc = ", max_loc)
    print("threshold = ", threshold)

    # If the maximum value is greater than the threshold, then we have a match
    if max_val > threshold:
        w, h = template_gray.shape[::-1]
        top_left = max_loc
        bottom_right = (top_left[0] + w, top_left[1] + h)
        cv2.rectangle(image, top_left, bottom_right, (0, 255, 0), 2)
        cv2.imwrite('/home/boby/found.png', image)
        print("Image created successfully")
        return True
    else:
        return False

template_path = sys.argv[1]
image_path = sys.argv[2]

if template_matching(image_path, template_path):
    print("True")
else:
    print("False")
