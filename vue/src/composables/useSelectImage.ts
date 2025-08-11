import type { ImageInterface } from "@/types/interfaces";
import { ref } from "vue";

export function useSelectImage(multiple?: boolean) {
  const selectedImages = ref<ImageInterface[] | null>(null);

  const selectImage = () => {
    const mediaFrame = wp.media({
      title: "Select File",
      button: {
        text: "Use this file"
      },
      multiple: multiple ? "add" : false
    });

    mediaFrame.on("select", function () {
      const selection = mediaFrame
        .state()
        .get("selection")
        .map((attachment: any) => attachment.toJSON());

      if (multiple) {
        const arr = selection;

        if (selectedImages.value) {
          arr.unshift(...selectedImages.value);
        }

        selectedImages.value = arr;
      } else {
        selectedImages.value = [selection[0]];
      }
    });

    mediaFrame.open();
  };

  return {
    selectedImages,
    selectImage
  };
}
