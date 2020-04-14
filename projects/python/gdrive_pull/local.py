import numpy as np
import requests
r = requests.post(
    "https://script.google.com/macros/s/AKfycby0gLxnDXDMWR8gtKOO1IL27co4zXdur6wIv72e3dbiKfN_YFE/exec",
    data={"id": "1YcOjDIErAepSYSUU4_SirkPdypfgSlKk"}
)
f = open(r.json()["name"], "bw")
f.write(np.array(r.json()["result"], dtype=np.uint8))
f.close()
print("Filename = {0}, MimeType = {1}".format(r.json()["name"], r.json()["mimeType"]))