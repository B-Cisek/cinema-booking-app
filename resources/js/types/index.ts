import type { Auth } from './auth';

export type * from './auth';

export interface Cinema {
    id: string;
    street: string;
    city: string;
}

export interface ScheduleDay {
    date: string;
    label: string;
    relative_label: string;
}

export interface ScreeningMovie {
    title: string;
    description: string;
    duration: number;
    poster_url: string;
}

export interface ScreeningHall {
    label: string;
}

export interface GlobalLang {
    button: {
        cinema_picker: string;
    };
    modal: {
        header: string;
        title: string;
        description: string;
        select: string;
        selected: string;
        empty_result: string;
        input_placeholder: string;
    };
}

export interface Screening {
    id: string;
    date: string;
    starts_at: string;
    ends_at: string;
    status: string;
    hall: ScreeningHall;
    movie: ScreeningMovie;
}

export type SeatType = 'standard' | 'vip' | 'wheelchair' | 'couple';
export type Row =
    | 'A'
    | 'B'
    | 'C'
    | 'D'
    | 'E'
    | 'F'
    | 'G'
    | 'H'
    | 'I'
    | 'J'
    | 'K'
    | 'L';

export interface Seat {
    id: string;
    row: Row;
    seatNumber: number;
    seatType: SeatType;
    posX: number;
    posY: number;
    isActive: boolean;
    isBooked: boolean;
}

export interface HallRow {
    label: Row;
    seats: Array<Seat | null>;
}

export interface SharedPageProps<TLang = Record<string, unknown>> {
    [key: string]: unknown;
    name: string;
    cinemas: Cinema[];
    selectedCinema: Cinema | null;
    globalLang: GlobalLang;
    auth: Auth;
    lang: TLang;
}

export type PageProps = SharedPageProps;
