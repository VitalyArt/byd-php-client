<?php

declare(strict_types=1);

namespace Byd\ApiClient\Enum;

use function in_array;

enum ApiRegion: string
{
    case EUROPE = 'https://dilinkappoversea-eu.byd.auto';
    case SOUTHEAST_ASIA = 'https://dilinkappoversea-sg.byd.auto';
    case OCEANIA = 'https://dilinkappoversea-au.byd.auto';
    case BRAZIL = 'https://dilinkappoversea-br.byd.auto';
    case JAPAN = 'https://dilinkappoversea-jp.byd.auto';
    case UZBEKISTAN = 'https://dilinkappoversea-uz.byd.auto';
    case MIDDLE_EAST_AFRICA = 'https://dilinkappoversea-no.byd.auto';
    case LATIN_AMERICA = 'https://dilinkappoversea-mx.byd.auto';
    case INDONESIA = 'https://dilinkappoversea-id.byd.auto';
    case TURKEY = 'https://dilinkappoversea-tr.byd.auto';
    case SOUTH_KOREA = 'https://dilinkappoversea-kr-ali.byd.auto';
    case INDIA = 'https://dilinkappoversea-in.byd.auto';
    case VIETNAM = 'https://dilinkappoversea-vn.byd.auto';
    case SAUDI_ARABIA = 'https://dilinkappoversea-sa.byd.auto';
    case OMAN = 'https://dilinkappoversea-om.byd.auto';
    case KAZAKHSTAN = 'https://dilinkappoversea-kz.byd.auto';

    public static function forCountry(CountryCode $countryCode): self
    {
        foreach (self::cases() as $region) {
            if (in_array($countryCode, $region->countries(), true)) {
                return $region;
            }
        }

        return self::EUROPE;
    }

    public function node(): int
    {
        return match ($this) {
            self::EUROPE => 1,
            self::SOUTHEAST_ASIA => 2,
            self::OCEANIA => 3,
            self::BRAZIL => 4,
            self::JAPAN => 5,
            self::UZBEKISTAN => 6,
            self::MIDDLE_EAST_AFRICA => 7,
            self::LATIN_AMERICA => 8,
            self::INDONESIA => 9,
            self::TURKEY => 10,
            self::SOUTH_KOREA => 11,
            self::INDIA => 12,
            self::VIETNAM => 13,
            self::SAUDI_ARABIA => 14,
            self::OMAN => 15,
            self::KAZAKHSTAN => 16,
        };
    }

    /** @return non-empty-list<CountryCode> */
    public function countries(): array
    {
        return match ($this) {
            self::EUROPE => [CountryCode::NO, CountryCode::NL, CountryCode::DE, CountryCode::DK, CountryCode::SE, CountryCode::FR, CountryCode::AT, CountryCode::LU, CountryCode::BE, CountryCode::FI, CountryCode::IT, CountryCode::ES, CountryCode::PT, CountryCode::GB, CountryCode::IE, CountryCode::IS, CountryCode::IL, CountryCode::HU, CountryCode::MT, CountryCode::GR, CountryCode::CH, CountryCode::PL, CountryCode::CY, CountryCode::EE, CountryCode::LV, CountryCode::LT, CountryCode::CZ, CountryCode::RO, CountryCode::SK, CountryCode::SI, CountryCode::BG, CountryCode::HR, CountryCode::LI, CountryCode::ME, CountryCode::RS, CountryCode::BA, CountryCode::MK, CountryCode::AL, CountryCode::MD, CountryCode::MC, CountryCode::VA, CountryCode::XK, CountryCode::UA],
            self::SOUTHEAST_ASIA => [CountryCode::SG, CountryCode::TH, CountryCode::MY, CountryCode::HK, CountryCode::MO, CountryCode::KH, CountryCode::LA, CountryCode::PH, CountryCode::BN, CountryCode::MM, CountryCode::NP, CountryCode::BD, CountryCode::PK, CountryCode::LK, CountryCode::PF, CountryCode::NC, CountryCode::MN, CountryCode::BT, CountryCode::MV],
            self::OCEANIA => [CountryCode::AU, CountryCode::NZ],
            self::BRAZIL => [CountryCode::BR],
            self::JAPAN => [CountryCode::JP],
            self::UZBEKISTAN => [CountryCode::UZ],
            self::MIDDLE_EAST_AFRICA => [CountryCode::PS, CountryCode::AE, CountryCode::IQ, CountryCode::KW, CountryCode::QA, CountryCode::MA, CountryCode::BH, CountryCode::JO, CountryCode::ZA, CountryCode::RE, CountryCode::MU, CountryCode::EG],
            self::LATIN_AMERICA => [CountryCode::MX, CountryCode::CL, CountryCode::UY, CountryCode::CO, CountryCode::DO, CountryCode::CR, CountryCode::PE, CountryCode::EC, CountryCode::PY, CountryCode::BO, CountryCode::PA, CountryCode::GT, CountryCode::SV, CountryCode::HN, CountryCode::NI, CountryCode::AR, CountryCode::BZ, CountryCode::BS, CountryCode::AW, CountryCode::CW, CountryCode::BQ, CountryCode::TT, CountryCode::JM, CountryCode::SR, CountryCode::KY, CountryCode::AG, CountryCode::GY, CountryCode::LC, CountryCode::BB],
            self::INDONESIA => [CountryCode::ID],
            self::TURKEY => [CountryCode::TR],
            self::SOUTH_KOREA => [CountryCode::KR],
            self::INDIA => [CountryCode::IN],
            self::VIETNAM => [CountryCode::VN],
            self::SAUDI_ARABIA => [CountryCode::SA],
            self::OMAN => [CountryCode::OM],
            self::KAZAKHSTAN => [CountryCode::KZ],
        };
    }
}
