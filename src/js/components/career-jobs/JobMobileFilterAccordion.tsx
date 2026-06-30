import React from 'react'
import type { JobFilterOption } from './types'

interface JobMobileFilterAccordionProps {
  label: string
  options: JobFilterOption[]
  selected: string
  isOpen: boolean
  onToggle: () => void
  onChange: (value: string) => void
}

const JobMobileFilterAccordion: React.FC<JobMobileFilterAccordionProps> = ({ label, options, selected, isOpen, onToggle, onChange }) => {
  const selectedOption = options.find((o) => o.slug === selected)
  const display = selectedOption ? selectedOption.name : 'All'

  return (
    <div>
      <button onClick={onToggle} className="flex w-full items-center justify-between px-24 py-10">
        <span className="fp-captital-title uppercase">
          <span className="text-white/50">{label}:</span> <span className="text-white">{display}</span>
        </span>
        <svg className={`h-24 w-24 text-white transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`}>
          <use href="#icon-arrow-down" />
        </svg>
      </button>

      <div className={`fp-collapse ${isOpen ? 'open' : ''}`}>
        <div className="overflow-hidden">
          <div className="pb-16">
            <button onClick={() => onChange('')} className="block w-full px-24 active:bg-white/5">
              <span className="flex min-h-45 w-full items-center justify-between border-b border-white/15 py-10">
                <span className={`fp-captital-title-sm uppercase ${!selected ? 'text-white' : 'text-white/50'}`}>All</span>
                {!selected && (
                  <svg className="h-24 w-24 text-white">
                    <use href="#icon-check" />
                  </svg>
                )}
              </span>
            </button>

            {options.map((opt) => (
              <button key={opt.slug} onClick={() => onChange(opt.slug)} className="block w-full px-24 transition-colors active:bg-white/5">
                <span className="flex min-h-45 w-full items-center justify-between border-b border-white/15 py-10">
                  <span className={`fp-captital-title-sm uppercase ${selected === opt.slug ? 'text-white' : 'text-white/50'}`}>{opt.name}</span>
                  {selected === opt.slug && (
                    <svg className="h-24 w-24 text-white">
                      <use href="#icon-check" />
                    </svg>
                  )}
                </span>
              </button>
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}

export default JobMobileFilterAccordion
