import React, { useState, useRef, useEffect } from 'react'

interface DesktopSearchProps {
  value: string
  onChange: (value: string) => void
}

const DesktopSearch: React.FC<DesktopSearchProps> = ({ value, onChange }) => {
  const [localValue, setLocalValue] = useState(value ?? '')
  const [isOpen, setIsOpen] = useState(!!localValue)
  const inputRef = useRef<HTMLInputElement>(null)
  const containerRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    setLocalValue(value ?? '')
  }, [value])

  useEffect(() => {
    if (isOpen) {
      inputRef.current?.focus()
    }
  }, [isOpen])

  useEffect(() => {
    const handleClickOutside = (e: MouseEvent) => {
      if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
        if (!localValue) setIsOpen(false)
      }
    }
    document.addEventListener('mousedown', handleClickOutside)
    return () => document.removeEventListener('mousedown', handleClickOutside)
  }, [localValue])

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    onChange(localValue)
  }

  const handleOpen = () => {
    if (!isOpen) {
      setIsOpen(true)
      return
    }
    onChange(localValue)
  }

  const handleClear = () => {
    setLocalValue('')
    onChange('')
    setIsOpen(false)
  }

  return (
    <div ref={containerRef} className="ml-auto flex items-center justify-end">
      <form onSubmit={handleSubmit} className="fp-btn-corners flex h-64 w-fit items-center bg-transparent p-0 focus-within:border-white/20">
        <span>
          <span className={`flex w-0 flex-1 items-center overflow-hidden transition-[width] ${isOpen ? 'w-230' : ''}`}>
            <span className="flex-1 pl-20">
              <input
                ref={inputRef}
                type="text"
                value={localValue}
                onChange={(e) => setLocalValue(e.target.value)}
                placeholder="Search"
                className="fp-captital-title-sm w-full bg-transparent text-white placeholder:text-white/50 focus:outline-none"
              />
            </span>
            {isOpen && localValue && (
              <button type="button" aria-label="Clear search" className="shrink-0 py-16 pl-8 text-white/50 hover:text-white" onClick={handleClear}>
                <svg className="h-20 w-20">
                  <use href="#icon-close" />
                </svg>
              </button>
            )}
          </span>
        </span>
        <button type="button" aria-label="Search" className="flex size-64 shrink-0 items-center justify-center" onClick={handleOpen}>
          <svg className="h-24 w-24 text-white">
            <use href="#icon-search" />
          </svg>
        </button>
      </form>
    </div>
  )
}

export default DesktopSearch
