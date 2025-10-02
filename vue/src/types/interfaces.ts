export interface ImageInterface {
  id: number;
  title: string;
  filename: string;
  url: string;
  link: string;
  alt: string;
  author: string;
  description: string;
  caption: string;
  name: string;
  status: string;
  uploadedTo: number;
  date: string;
  modified: string;
  menuOrder: number;
  mime: string;
  type: string;
  subtype: string;
  icon: string;
  dateFormatted: string;
  nonces: Nonces;
  editLink: string;
  meta: boolean;
  authorName: string;
  authorLink: string;
  filesizeInBytes: number;
  filesizeHumanReadable: string;
  context: string;
  height: number;
  width: number;
  orientation: string;
  sizes: Sizes;
  compat: Compat;
}

interface Compat {
  item: string;
  meta: string;
}

interface Sizes {
  thumbnail: Thumbnail;
  medium: Thumbnail;
  full: Thumbnail;
}

interface Thumbnail {
  height: number;
  width: number;
  url: string;
  orientation: string;
}

interface Nonces {
  update: string;
  delete: string;
  edit: string;
}

export interface Meta {
  slideEffect: string;
  columns: Columns;
  paddingTop: string;
  spaceBetween: string;
  clickAction: string;
  orderBy: string;
  infiniteLoop: boolean;
  sliderDirection: "horizontal" | "vertical";
  centerSlides: boolean;
  autoplay: boolean;
  autoplayDelay: string;
  reversedDirection: boolean;
  pauseonhover: boolean;
  stopOnLastSlide: boolean;
  navigation: boolean;
  navigationActiveColor: string;
  pagination: boolean;
  paginationStyle: string;
  scrollbarBackground: string;
  paginationActiveColor: string;
  clickable: boolean;
  paginationMargin: PaginationMargin;
}

interface PaginationMargin {
  top: string;
  right: string;
  down: string;
  left: string;
}

interface Columns {
  desktop: string;
  laptop: string;
  tablet: string;
  mobile: string;
}

export interface SlidersDataInterface {
  data: Slider[];
  page: number;
  per_page: number;
  total: number;
}

export interface Slider {
  id: number;
  title: string;
  slides: Slide[];
  meta: Meta;
  created_at: string;
  updated_at: string;
}

export interface Slide {
  url: string;
  title?: string;
  description?: string;
}
