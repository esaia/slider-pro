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
  spaceBetween: number;
  clickAction: string;
  orderBy: string;
  infiniteLoop: boolean;
  sliderDirection: string;
  autoplay: boolean;
  sliderSpeed: number;
  sliderOrientation: string;
  pauseonhover: boolean;
  navigation: boolean;
  navigationPosition: string;
  navigationColors: NavigationColors;
  pagination: boolean;
  paginationStyle: string;
  paginationColors: NavigationColors;
  paginationMargin: PaginationMargin;
}

interface PaginationMargin {
  top: number;
  right: number;
  down: number;
  left: number;
}

interface NavigationColors {
  color: string;
  active: string;
}

interface Columns {
  desktop: number;
  laptop: number;
  tablet: number;
  mobile: number;
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
