import React from 'react'
import DlcCard from './DlcCard'
import type { DlcPost } from './types'

interface DlcGridProps {
  posts: DlcPost[]
  highlight?: string
}

const DlcGrid: React.FC<DlcGridProps> = ({ posts, highlight }) => {
  return (
    <div className="space-y-32">
      {posts.map((dlc) => (
        <DlcCard key={dlc.id} dlc={dlc} highlight={highlight} />
      ))}
    </div>
  )
}

export default DlcGrid
